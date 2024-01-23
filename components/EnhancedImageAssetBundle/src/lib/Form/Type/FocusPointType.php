<?php

/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\Form\Type;

use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'posX',
                TextType::class,
                [
                    'label'      => /* @Desc("X") */ 'content.field_type.enhancedimage.focuspoint.posX',
                    'attr'       => [
                        'class' => 'focuspoint-helper--input-focus-x',
                    ],
                ]
            )
            ->add(
                'posY',
                TextType::class,
                [
                    'label'      => /* @Desc("Y") */ 'content.field_type.enhancedimage.focuspoint.posY',
                    'attr'       => [
                        'class' => 'focuspoint-helper--input-focus-y',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => FocusPoint::class]);
    }
}
