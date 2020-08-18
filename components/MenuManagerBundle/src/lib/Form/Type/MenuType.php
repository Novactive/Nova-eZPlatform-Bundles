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

namespace Novactive\EzMenuManager\Form\Type;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    /** @var ConfigResolverInterface */
    protected $configResolver;

    /**
     * @param ConfigResolverInterface $configResolver
     * @required
     */
    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

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
        $menuTypes = $this->configResolver->getParameter('menu_types', 'nova_menu_manager') ?? [];
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
                'remoteId',
                HiddenType::class,
                [
                    'label'              => 'menu.property.remote_id',
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
        if (!empty($menuTypes)) {
            $builder
                ->add(
                    'type',
                    ChoiceType::class,
                    [
                        'label'                     => 'menu.property.type',
                        'translation_domain'        => 'menu_manager',
                        'choices'                   => array_combine(array_values($menuTypes), array_keys($menuTypes)),
                        'choice_translation_domain' => 'menu_manager',
                        'required'                  => false,
                    ]
                );
        }
    }
}
