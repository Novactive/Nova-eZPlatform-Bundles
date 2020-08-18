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

use Novactive\EzMenuManager\Service\DataTransformer\MenuItemsCollectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemsCollectionType extends AbstractType
{
    /** @var MenuItemsCollectionTransformer */
    protected $menuItemsCollectionTransformer;

    /**
     * MenuItemsCollectionType constructor.
     */
    public function __construct(MenuItemsCollectionTransformer $menuItemsCollectionTransformer)
    {
        $this->menuItemsCollectionTransformer = $menuItemsCollectionTransformer;
    }

    public function getParent()
    {
        return TextareaType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->menuItemsCollectionTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            []
        );
    }
}
