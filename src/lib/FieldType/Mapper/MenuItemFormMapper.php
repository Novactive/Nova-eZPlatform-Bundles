<?php

namespace Novactive\EzMenuManager\FieldType\Mapper;

use eZ\Publish\API\Repository\LocationService;
use EzSystems\RepositoryForms\Data\Content\ContentCreateData;
use EzSystems\RepositoryForms\Data\Content\ContentUpdateData;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Novactive\EzMenuManager\Form\Type\FieldType\MenuItemFieldType;
use Novactive\EzMenuManager\Service\MenuService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /** @var MenuService */
    protected $menuService;

    /** @var LocationService */
    protected $locationService;

    /**
     * MenuItemFormMapper constructor.
     *
     * @param MenuService     $menuService
     * @param LocationService $locationService
     */
    public function __construct(MenuService $menuService, LocationService $locationService)
    {
        $this->menuService     = $menuService;
        $this->locationService = $locationService;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig      = $fieldForm->getConfig();
        $names           = $fieldDefinition->getNames();
        $label           = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);
        //        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();

        $formData          = $fieldForm->getParent()->getParent()->getData();
        $parentLocationsId = [];
        if ($formData instanceof ContentCreateData) {
            foreach ($formData->getLocationStructs() as $locationStruct) {
                $parentLocationsId[] = $locationStruct->parentLocationId;
            }
        } elseif ($formData instanceof ContentUpdateData) {
            $parentLocations = $this->locationService->loadLocations($formData->contentDraft->contentInfo);
            foreach ($parentLocations as $parentLocation) {
                $parentLocationsId[] = $parentLocation->id;
            }
        }

        $availableMenus = [];
        foreach ($parentLocationsId as $parentLocationId) {
            $menus = $this->menuService->getAvailableMenuForLocationId($parentLocationId);
            foreach ($menus as $menu) {
                if (!isset($availableMenus[$menu->getId()])) {
                    $availableMenus[$menu->getId()] = [
                        'menu'               => $menu,
                        'parentLocationsIds' => [],
                    ];
                }
                $availableMenus[$menu->getId()]['parentLocationsIds'][] = $parentLocationId;
            }
        }
        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                           ->create(
                               'value',
                               MenuItemFieldType::class,
                               [
                                   'required'        => $fieldDefinition->isRequired,
                                   'label'           => $label,
                                   'available_menus' => $availableMenus,
                               ]
                           )
                           ->setAutoInitialize(false)
                           ->getForm()
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'ezrepoforms_content_type',
                ]
            );
    }
}
