<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Salesforce;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesforceApiCredentialsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('clientId', TextType::class, [
            'label' => /* @Desc("Client ID") */ 'salesforce_api_credentials.form.client_id.label',
            'required' => true,
        ]);
        $builder->add('clientSecret', TextType::class, [
            'label' => /* @Desc("Client secret") */ 'salesforce_api_credentials.form.client_secret.label',
            'required' => true,
        ]);
        $builder->add('username', TextType::class, [
            'label' => /* @Desc("Username") */ 'salesforce_api_credentials.form.username.label',
            'required' => true,
        ]);
        $builder->add('password', TextType::class, [
            'label' => /* @Desc("Password") */ 'salesforce_api_credentials.form.password.label',
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => SalesforceApiCredentials::class,
            'translation_domain' => 'forms',
        ]);
    }
}
