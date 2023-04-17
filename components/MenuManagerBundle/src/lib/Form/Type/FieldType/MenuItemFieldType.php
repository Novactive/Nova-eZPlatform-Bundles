<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\LocationService;
use Ibexa\Core\Helper\TranslationHelper;
use EzSystems\EzPlatformContentForms\Data\Content\ContentCreateData;
use EzSystems\EzPlatformContentForms\Data\Content\ContentUpdateData;
use Novactive\EzMenuManager\Service\DataTransformer\MenuItemValueTransformer;
use Novactive\EzMenuManager\Service\MenuService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuItemFieldType extends AbstractType
{
    /** @var FieldTypeService */
    protected $fieldTypeService;

    /** @var MenuService */
    protected $menuService;

    /** @var LocationService */
    protected $locationService;

    /** @var MenuItemValueTransformer */
    protected $fieldValueTransformer;

    /** @var TranslationHelper */
    protected $translationHelper;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * MenuItemFieldType constructor.
     */
    public function __construct(
        FieldTypeService $fieldTypeService,
        MenuService $menuService,
        LocationService $locationService,
        MenuItemValueTransformer $fieldValueTransformer,
        TranslationHelper $translationHelper,
        TranslatorInterface $translator
    ) {
        $this->fieldTypeService = $fieldTypeService;
        $this->menuService = $menuService;
        $this->locationService = $locationService;
        $this->fieldValueTransformer = $fieldValueTransformer;
        $this->translationHelper = $translationHelper;
        $this->translator = $translator;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_menuitem';
    }

    public function getParent()
    {
        return TextareaType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->fieldValueTransformer);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attributes = [];
        $view->vars['attr'] = array_merge($view->vars['attr'], $attributes);
        $view->vars['menu_items'] = $form->getData()->menuItems;

        $formData = $form->getParent()->getParent()->getParent()->getData();
        $parentLocationsId = [];
        if ($formData instanceof ContentCreateData) {
            $view->vars['content_name'] = $this->translator->trans(
                'new_content_item',
                ['%contentType%' => $this->translationHelper->getTranslatedByMethod($formData->contentType, 'getName')],
                'content_create'
            );
            foreach ($formData->getLocationStructs() as $locationStruct) {
                $parentLocationsId[] = $locationStruct->parentLocationId;
            }
        } elseif ($formData instanceof ContentUpdateData) {
            $view->vars['content_name'] = $this->translationHelper->getTranslatedByMethod(
                $formData->contentDraft,
                'getName'
            );

            $locations = $this->locationService->loadLocations($formData->contentDraft->contentInfo);

            foreach ($locations as $location) {
                $parentLocationsId[] = $location->parentLocationId;
            }
        }

        $availableMenus = [];
        foreach ($parentLocationsId as $parentLocationId) {
            $menus = $this->menuService->getAvailableMenuForLocationId($parentLocationId);
            if (!empty($menus)) {
                $parentLocation = $this->locationService->loadLocation($parentLocationId);
                foreach ($menus as $menu) {
                    if (!isset($availableMenus[$menu->getId()])) {
                        $availableMenus[$menu->getId()] = [
                            'menu' => $menu,
                            'defaultParentMenuItems' => [],
                        ];
                    }

                    $availableMenus[$menu->getId()]['defaultParentMenuItems'] +=
                        $this->menuService->getLocationMenuItemsInMenu(
                            $parentLocation,
                            $menu
                        )->getValues();
                }
            }
        }

        usort($availableMenus, function ($first, $second) {
            return strcmp($first['menu']->getName(), $second['menu']->getName());
        });
        $view->vars['available_menus'] = $availableMenus;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            []
        );
    }
}
