<?php
/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

namespace Novactive\EzEnhancedImageAsset\Form\Type\FieldType;

use EzSystems\RepositoryForms\Form\Type\FieldType\ImageFieldType;
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
     * @return string|null
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_enhancedimage';
    }

    /**
     * @return string|null
     */
    public function getParent()
    {
        return ImageFieldType::class;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                    'label'      => /* @Desc("Is new focus point ?") */
                    'content.field_type.enhancedimage.isNewFocusPoint',
                    'attr'       => [
                        'class' => 'focuspoint-helper--cb-is-new',
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['translation_domain' => 'ezrepoforms_fieldtype']);
    }
}
