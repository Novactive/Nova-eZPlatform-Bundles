<?php

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes\FieldType\AcxSelection\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AcxSelectionType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_acxselection';
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new FieldValueTransformer($options['multiple']));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded' => false,
            'required' => true,
            'multiple' => false,
            'choices' => [],
        ]);
    }
}
