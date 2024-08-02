<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Mdb;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\FileReaderOptionsFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MdbReaderOptionsFormType extends FileReaderOptionsFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('queryString', TextType::class, [
            'label' => /* @Desc("Query string") */ 'mdb_reader.form.options.query_string.label',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                                   'data_class' => MdbReader::getOptionsType(),
                                   'translation_domain' => 'forms',
                               ]);
    }
}
