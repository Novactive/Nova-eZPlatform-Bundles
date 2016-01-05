<?php
/**
 * NovaeZExtraBundle SimpleSearch Form Type
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class Search
 */
class SimpleSearch extends AbstractType
{

    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->setMethod( 'GET' )
            ->add( 'query' )
            ->add(
                'filters',
                'collection',
                [
                    'type'    => 'hidden',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'label' => false,
                    'required' => false
                ]
            )
            ->add( 'search', 'submit' );
    }

    /**
     * Set Default Option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
                'data_class'      => 'Novactive\Bundle\eZExtraBundle\Core\Helper\Search\Structure',
            ]
        );
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return 'novactive_ezextra_simple_search';
    }
}
