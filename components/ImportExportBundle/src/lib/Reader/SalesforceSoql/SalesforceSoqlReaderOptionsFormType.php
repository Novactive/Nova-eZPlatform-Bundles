<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\SalesforceSoql;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptionsFormType;
use AlmaviaCX\Bundle\IbexaImportExport\Salesforce\SalesforceApiCredentialsFormType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesforceSoqlReaderOptionsFormType extends ReaderOptionsFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('queryString', TextType::class, [
            'label' => /* @Desc("Query string") */ 'salesforce_soql_reader.form.options.query_string.label',
            'required' => true,
        ])
            ->add('countQueryString', TextType::class, [
            'label' => /* @Desc("Count query string") */ 'salesforce_soql_reader.form.options.count_query_string.label',
            'required' => true,
        ])
            ->add('domain', TextType::class, [
            'label' => /* @Desc("Domain") */ 'salesforce_soql_reader.form.options.domain.label',
            'required' => true,
        ])
            ->add('version', TextType::class, [
            'label' => /* @Desc("Version") */ 'salesforce_soql_reader.form.options.version.label',
            'required' => true,
        ])
            ->add('credentials', SalesforceApiCredentialsFormType::class, [
            'label' => /* @Desc("Credentials") */ 'salesforce_soql_reader.form.options.credentials.label',
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                                   'data_class' => SalesforceSoqlReader::getOptionsType(),
                                   'translation_domain' => 'forms',
                               ]);
    }
}
