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

namespace Novactive\EzEnhancedImageAsset\Form\Type\FieldType;

use Ibexa\ContentForms\Form\Type\FieldType\ImageFieldType;
use Novactive\EzEnhancedImageAsset\Form\Type\FocusPointType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EnhancedImageFieldType.
 *
 * @package Novactive\EzEnhancedImageAsset\Form\Type\FieldType
 */
class EnhancedImageFieldType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'ezplatform_fieldtype_enhancedimage';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): ?string
    {
        return ImageFieldType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'focusPoint',
                FocusPointType::class,
                [
                    'label' => /* @Desc("Focus point") */
                    'content.field_type.enhancedimage.focuspoint',
                ]
            )
            ->add(
                'isNewFocusPoint',
                CheckboxType::class,
                [
                    'label' => /* @Desc("Is new focus point ?") */
                    'content.field_type.enhancedimage.isNewFocusPoint',
                    'attr' => [
                        'class' => 'focuspoint-helper--cb-is-new',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                                   'translation_domain' => 'ezplatform_content_forms_fieldtype',
                                   'is_alternative_text_required' => false,
                               ]);

        $resolver->setAllowedTypes('is_alternative_text_required', 'bool');
    }
}
