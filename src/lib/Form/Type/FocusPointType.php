<?php

namespace Novactive\EzEnhancedImageAsset\Form\Type;

use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */
class FocusPointType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'posX',
                HiddenType::class,
                [
                    'label'      => /* @Desc("X") */ 'content.field_type.enhancedimage.focuspoint.posX',
                    'empty_data' => 0,
                    'attr'       => [
                        'class' => 'focuspoint-helper--input-focus-x',
                    ],
                ]
            )
            ->add(
                'posY',
                HiddenType::class,
                [
                    'label'      => /* @Desc("Y") */ 'content.field_type.enhancedimage.focuspoint.posY',
                    'empty_data' => 0,
                    'attr'       => [
                        'class' => 'focuspoint-helper--input-focus-y',
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => FocusPoint::class]);
    }
}
