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

namespace Novactive\EzRssFeedBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TreeDiscoveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'location',
                IntegerType::class,
                [
                    'attr' => ['hidden' => true],
                    'empty_data' => [],
                ]
            )
            ->addModelTransformer($this->getDataTransformer());
    }

    private function getDataTransformer(): DataTransformerInterface
    {
        return new CallbackTransformer(
            function ($value) {
                if (null === $value || 0 === $value) {
                    return $value;
                }

                return ['location' => !empty($value) ? $value : null];
            },
            function ($value) {
                if (\is_array($value) && array_key_exists('location', $value)) {
                    return $value['location'] ?? null;
                }

                return $value;
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function getName()
    {
        return 'tree_discovery';
    }
}
