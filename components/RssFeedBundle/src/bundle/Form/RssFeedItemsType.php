<?php

/**
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 */

namespace Novactive\EzRssFeedBundle\Form;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use Novactive\EzRssFeedBundle\Entity\RssFeedItems;
use Novactive\EzRssFeedBundle\Form\Type\TreeDiscoveryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RssFeedItemsType extends AbstractType
{
    protected ContentTypeService $contentTypeService;
    protected ConfigResolverInterface $configResolver;
    protected TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService;
    protected array $fieldTypeMap = [];
    protected array $taxonomyByField = [];

    public function __construct(
        ContentTypeService $contentTypeService,
        ConfigResolverInterface $configResolver,
        TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->configResolver = $configResolver;
        $this->taxonomyEntryAssignmentService = $taxonomyEntryAssignmentService;
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $contentTypeList = $this->getContentTypeList();
        $fieldTypeMap = $this->fieldTypeMap;
        $taxonomyByField = $this->taxonomyByField;

        $builder
            ->add(
                'subtree_path',
                TreeDiscoveryType::class,
                [
                    'label' => 'ez_rss_feed.form.subtree_path',
                    'compound' => true,
                    'attr' => [
                        'class' => 'ibexa-button-tree 
                            pure-button 
                            ibexa-font-icon 
                            ibexa-btn btn 
                            ibexa-btn--secondary
                            js-novaezrssfeed-select-location-id',
                        'data-location-input-selector' => 'input-location-'.uniqid('', false),
                        'data-selected-location-list-selector' => 'location-values-'.uniqid('', false),
                    ],
                ]
            )
            ->add(
                'include_subtree',
                CheckboxType::class,
                [
                    'label' => 'ez_rss_feed.form.include_subtree',
                    'required' => false,
                ]
            )
            ->add(
                'contenttype_id',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.contenttype',
                    'choices' => $contentTypeList,
                ]
            )->add(
                'chosenTaxonomy',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.chosen_taxonomy',
                    'required' => false,
                    'choices' =>  $taxonomyByField,
                    'empty_data' => null,
                ]
            )->add(
                'tag',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.taxonomy_value', // Tag
                    'required' => false,
                    'choices' => $taxonomyByField,
                    'empty_data' => null,
                ]
            )
            ->add(
                'title',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.title',
                    'choices' => $this->fieldTypeMap,
                ]
            )
            ->add(
                'description',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.description',
                    'required' => false,
                    'choices' => $fieldTypeMap,
                    'empty_data' => null,
                ]
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.category',
                    'required' => false,
                    'choices' => $fieldTypeMap,
                    'empty_data' => null,
                ]
            )->add(
                'media',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.media',
                    'required' => false,
                    'choices' => $fieldTypeMap,
                    'empty_data' => null,
                ]
            );

        $formModifier = function (FormInterface $form, ContentType $contentType) {
            // Choices pour les champs title, description, category et média
            $fieldTypeMap = $this->getFieldTypeByContentType($contentType);
            // Choices pour le filtre par taxonomy. (Permet de choisir quelle taxonomy)
            $fieldTypeMapTaxonomy = $this->getFieldTypeByTaxonomy($contentType);
            // Choices pour la valeur du filtre par taxonomy. (Permet de choisir la valeur de la taxonomy)
            $taxonomyByField = $this->getTaxonomyByFieldType($fieldTypeMapTaxonomy, $contentType);

            dump([
                '$fieldTypeMap' => $fieldTypeMap,
                '$fieldTypeMapTaxonomy' => $fieldTypeMapTaxonomy,
                '$taxonomyByField' => $taxonomyByField,
            ]);

            $form->add(
                'chosenTaxonomy',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.chosen_taxonomy',
                    'required' => false,
                    'choices' =>  $fieldTypeMapTaxonomy,
                    'empty_data' => null,
                ]
            );
            $form->add(
                'taxonomy',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.taxonomy_value', // tag
                    'required' => false,
                    'choices' => $taxonomyByField,
                    'empty_data' => null,
                ]
            );
            $form->add(
                'title',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.field.title',
                    'choices' => $fieldTypeMap,
                ]
            );

            $fieldTypeMap = array_merge(['[Passer]' => ''], $fieldTypeMap);
            // $fieldTypeMapTax = array_merge(['[Passer]' => ''], $fieldTypeMapTax);
            $form->add(
                'description',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.field.description',
                    'required' => false,
                    'choices' => $fieldTypeMap,
                    'empty_data' => null,
                ]
            );
            $form->add(
                'category',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.field.category',
                    'required' => false,
                    'choices' => $fieldTypeMap,
                    'empty_data' => null,
                ]
            );
            $form->add(
                'media',
                ChoiceType::class,
                [
                    'label' => 'ez_rss_feed.form.field.media',
                    'required' => false,
                    'choices' => $fieldTypeMap,
                    'empty_data' => null,
                ]
            );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                if (null !== $data) {
                    $contentTypeId = $data->getContentTypeId();
                    $contentType = $this->contentTypeService->loadContentType($contentTypeId);
                    $formModifier($event->getForm(), $contentType);
                }
            }
        );

        $builder->get('contenttype_id')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $contentTypeId = $event->getData();
                $contentType = $this->contentTypeService->loadContentType($contentTypeId);
                $formModifier($event->getForm()->getParent(), $contentType);
            }
        );
    }

    public function getContentTypeList(): array
    {
        if ($this->configResolver->hasParameter('ez_rss_feed.content_group')) {
            $contentGroup = $this->configResolver->getParameter('ez_rss_feed.content_group');
        } else {
            $contentGroup = 'Content';
        }

        $contentTypesMap = [];
        $contentTypeGroupContent = null;

        /*
         * Maybe the content type group does not exist
         */
        try {
            $contentTypeGroupContent = $this->contentTypeService->loadContentTypeGroupByIdentifier($contentGroup);
        } catch (NotFoundException $e) {
            $contentTypeGroupContent = null;
        }

        try {
            if (null === $contentTypeGroupContent) {
                $contentTypeGroupContent = $this->contentTypeService->loadContentTypeGroupByIdentifier('Contenu');
            }

            $contentTypes = $this->contentTypeService->loadContentTypes($contentTypeGroupContent);

            foreach ($contentTypes as $contentType) {
                $contentTypesMap[ucfirst($contentType->getName())] = $contentType->id;
            }
            ksort($contentTypesMap);
            if (\count($contentTypesMap)) {
                $defaultContentType = $this->contentTypeService
                    ->loadContentType(array_values($contentTypesMap)[0]);
                $this->fieldTypeMap = $this->getFieldTypeByContentType($defaultContentType);
                $this->fieldTypeMapTax = $this->getFieldTypeByTaxonomy($defaultContentType);
            }
        } catch (NotFoundException $e) {
            return [];
        }

        return $contentTypesMap;
    }

    /**
     * Liste des fields du contentType
     * @param ContentType $contentType
     * @return array Label => identifier
     */
    public function getFieldTypeByContentType(ContentType $contentType): array
    {
        $fieldsMap = [];

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            $fieldsMap[ucfirst($fieldDefinition->getName())] = $fieldDefinition->identifier;
        }
        ksort($fieldsMap);

        return $fieldsMap;
    }

    /**
     * Liste des fields du contentType de type Taxonomy (`ibexa_taxonomy_entry_assignment`)
     * @param ContentType $contentType
     * @return array Label => identifier
     */
    public function getFieldTypeByTaxonomy(ContentType $contentType): array
    {
        $fieldsMapTaxonomy = [];

        foreach ($contentType->getFieldDefinitions()->filterByType('ibexa_taxonomy_entry_assignment') as $fieldDefinition) {
            $fieldsMapTaxonomy[ucfirst($fieldDefinition->getName())] = $fieldDefinition->identifier;
        }
        ksort($fieldsMapTaxonomy);

        return $fieldsMapTaxonomy;
    }

    /**
     * Liste/Arbo ? des tag
     * @param array $fieldTypeMapTax
     * @param ContentType $contentType
     * @return array
     */
    public function getTaxonomyByFieldType(array $fieldTypeMapTax, ContentType $contentType): array
    {
        dump('TODO', $fieldTypeMapTax, $contentType);
        // TODO
        return [];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => RssFeedItems::class,
            ]
        );
    }
}
