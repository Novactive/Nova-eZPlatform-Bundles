<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldTypeService;
use Novactive\EzMenuManager\Service\MenuService;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemFieldType extends AbstractType
{
    /** @var FieldTypeService */
    protected $fieldTypeService;

    /** @var MenuService */
    protected $menuService;

    /** @var FieldValueTransformer */
    protected $fieldValueTransformer;

    /**
     * MenuItemFieldType constructor.
     *
     * @param FieldTypeService      $fieldTypeService
     * @param MenuService           $menuService
     * @param FieldValueTransformer $fieldValueTransformer
     */
    public function __construct(
        FieldTypeService $fieldTypeService,
        MenuService $menuService,
        FieldValueTransformer $fieldValueTransformer
    ) {
        $this->fieldTypeService      = $fieldTypeService;
        $this->menuService           = $menuService;
        $this->fieldValueTransformer = $fieldValueTransformer;
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
//        $available_menus   = $options['available_menus'];
//        $parentLocationsId = $options['parentLocationsId'];
//        $data              = [];
//        foreach ($available_menus as $availableMenu) {
//            if (empty($parentLocationsId)) {
//                $ContentMenuItem = new MenuItem\ContentMenuItem();
//                $ContentMenuItem->setMenu($availableMenu);
//                $data[] = $ContentMenuItem;
//            } else {
//                foreach ($parentLocationsId as $parentLocationId) {
//                    $parentContentMenuItems = $this->menuService->getMenuItemsInMenuWithLocationId(
//                        $availableMenu,
//                        $parentLocationId
//                    );
//                    if (empty($parentContentMenuItems)) {
//                        $ContentMenuItem = new MenuItem\ContentMenuItem();
//                        $ContentMenuItem->setMenu($availableMenu);
//                        $data[] = $ContentMenuItem;
//                    } else {
//                        foreach ($parentContentMenuItems as $parentContentMenuItem) {
//                            $ContentMenuItem = new MenuItem\ContentMenuItem();
//                            $ContentMenuItem->setMenu($availableMenu);
//                            $ContentMenuItem->setParent($parentContentMenuItem);
//                            $data[] = $ContentMenuItem;
//                        }
//                    }
//                }
//            }
//        }

        $builder->addModelTransformer($this->fieldValueTransformer);
    }

    /**
     * @inheritDoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attributes = [];

        $view->vars['attr']            = array_merge($view->vars['attr'], $attributes);
        $view->vars['available_menus'] = $form->getConfig()->getOption('available_menus');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'available_menus' => [],
            ]
        );
    }
}
