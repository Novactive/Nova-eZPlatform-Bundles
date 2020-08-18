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
use Novactive\EzMenuManagerBundle\Entity\MenuSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuSearchType extends AbstractType
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
                    'data_class' => MenuSearch::class,
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
                    'required'           => false,
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
