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

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
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
    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var ConfigResolverInterface */
    protected $configResolver;

    /** @var array */
    protected $fieldTypeMap;

    /**
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService, ConfigResolverInterface $configResolver)
    {
        $this->contentTypeService = $contentTypeService;
        $this->configResolver     = $configResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contentTypeList = $this->getContentTypeList();
        $fieldTypeMap    = $this->fieldTypeMap;

        $builder
            ->add(
                'subtree_path',
                TreeDiscoveryType::class,
                [
                    'label'    => 'ez_rss_feed.form.subtree_path',
                    'compound' => true,
                    'attr'     => [
                        'class'                     => 'ez-button-tree 
                                                        pure-button 
                                                        ez-font-icon 
                                                        ez-button btn 
                                                        btn-primary 
                                                        ez-pick-subtree-button',
                        'data-starting-location-id' => $this->configResolver->getParameter(
                            'universal_discovery_widget_module.default_location_id'
                        ),
                        'data-location-input-selector'         => 'input-location-' . uniqid(),
                        'data-selected-location-list-selector' => 'location-values-' . uniqid(),
                    ],
                ]
            )
            ->add(
                'include_subtree',
                CheckboxType::class,
                [
                    'label'    => 'ez_rss_feed.form.include_subtree',
                    'required' => false,
                ]
            )
            ->add(
                'contenttype_id',
                ChoiceType::class,
                [
                    'label'   => 'ez_rss_feed.form.contenttype',
                    'choices' => $contentTypeList,
                ]
            )
            ->add(
                'title',
                ChoiceType::class,
                [
                    'label'   => 'ez_rss_feed.form.title',
                    'choices' => $this->fieldTypeMap,
                ]
            )
            ->add(
                'description',
                ChoiceType::class,
                [
                    'label'      => 'ez_rss_feed.form.description',
                    'required'   => false,
                    'choices'    => $fieldTypeMap,
                    'empty_data' => null,
                ]
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'label'      => 'ez_rss_feed.form.category',
                    'required'   => false,
                    'choices'    => $fieldTypeMap,
                    'empty_data' => null,
                ]
            )->add(
                'media',
                ChoiceType::class,
                [
                    'label'      => 'ez_rss_feed.form.media',
                    'required'   => false,
                    'choices'    => $fieldTypeMap,
                    'empty_data' => null,
                ]
            );

        $formModifier = function (FormInterface $form, ContentType $contentType = null) {
            $fieldTypeMap = $this->getFieldTypeByContentType($contentType);
            $form->add(
                'title',
                ChoiceType::class,
                [
                    'label'   => 'ez_rss_feed.form.field.title',
                    'choices' => $fieldTypeMap,
                ]
            );

            $fieldTypeMap = array_merge(['[Passer]' => ''], $fieldTypeMap);

            $form->add(
                'description',
                ChoiceType::class,
                [
                    'label'      => 'ez_rss_feed.form.field.description',
                    'required'   => false,
                    'choices'    => $fieldTypeMap,
                    'empty_data' => null,
                ]
            )
                 ->add(
                     'category',
                     ChoiceType::class,
                     [
                         'label'      => 'ez_rss_feed.form.field.category',
                         'required'   => false,
                         'choices'    => $fieldTypeMap,
                         'empty_data' => null,
                     ]
                 )->add(
                     'media',
                     ChoiceType::class,
                     [
                        'label'      => 'ez_rss_feed.form.field.media',
                        'required'   => false,
                        'choices'    => $fieldTypeMap,
                        'empty_data' => null,
                     ]
                 );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                if (null != $data) {
                    $contentTypeId = $data->getContentTypeId();
                    $contentType   = $this->contentTypeService->loadContentType($contentTypeId);

                    $formModifier($event->getForm(), $contentType);
                }
            }
        );

        $builder->get('contenttype_id')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $contentTypeId = $event->getData();
                $contentType   = $this->contentTypeService->loadContentType($contentTypeId);

                $formModifier($event->getForm()->getParent(), $contentType);
            }
        );
    }

    public function getContentTypeList()
    {
        try {
            $contentTypesMap         = [];
            $contentTypeGroupContent = $this->contentTypeService->loadContentTypeGroupByIdentifier('Content');
            $contentTypes            = $this->contentTypeService->loadContentTypes($contentTypeGroupContent);

            foreach ($contentTypes as $contentType) {
                $contentTypesMap[ucfirst($contentType->getName())] = $contentType->id;
            }
            ksort($contentTypesMap);
            if (\count($contentTypesMap)) {
                $defaultContentType = $this->contentTypeService
                    ->loadContentType(array_values($contentTypesMap)[0]);
                $this->fieldTypeMap = $this->getFieldTypeByContentType($defaultContentType);
            }
        } catch (NotFoundException $e) {
            return [];
        }

        return $contentTypesMap;
    }

    public function getFieldTypeByContentType(ContentType $contentType)
    {
        $fieldsMap = [];

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            $fieldsMap[ucfirst($fieldDefinition->getName())] = $fieldDefinition->identifier;
        }
        ksort($fieldsMap);

        return $fieldsMap;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => RssFeedItems::class,
            ]
        );
    }
}
