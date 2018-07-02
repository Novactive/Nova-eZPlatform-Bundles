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

namespace Novactive\EzMenuManager\Form\Type;

use Novactive\EzMenuManagerBundle\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => Menu::class,
                ]
            );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label'              => 'menu.property.name',
                    'translation_domain' => 'menu_manager',
                ]
            )
            ->add(
                'rootLocationId',
                MenuRootLocationType::class,
                [
                    'required'           => false,
                    'label'              => 'menu.property.root_location',
                    'translation_domain' => 'menu_manager',
                ]
            )
            ->add(
                'items',
                MenuItemsCollectionType::class,
                [
                    'required'           => false,
                    'label'              => 'menu.property.items',
                    'translation_domain' => 'menu_manager',
                ]
            );
    }
}
