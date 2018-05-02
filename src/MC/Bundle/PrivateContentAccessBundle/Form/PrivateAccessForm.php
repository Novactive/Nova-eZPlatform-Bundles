<?php

namespace MC\Bundle\PrivateContentAccessBundle\Form;

use EzSystems\RepositoryForms\Form\Type\FieldType\CheckboxFieldType;
use MC\Bundle\PrivateContentAccessBundle\Entity\PrivateAccess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PrivateAccessForm extends AbstractType
{
    public function getName()
    {
        return 'private_access_form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('activate', CheckboxFieldType::class)
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PrivateAccess::class,
        ));
    }
}