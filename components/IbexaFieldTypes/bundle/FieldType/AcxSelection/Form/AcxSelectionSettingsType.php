<?php

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes\FieldType\AcxSelection\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AcxSelectionSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'isMultiple',
            CheckboxType::class,
            [
                'required' => false,
                'label' => 'Multiple',
            ]);
        $builder->add('choices_entry', TextType::class);
        $builder->add('template', TextType::class);
    }
}