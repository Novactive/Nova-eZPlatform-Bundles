<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Csv;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\FileReaderOptionsFormType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvFileReaderOptionsFormType extends FileReaderOptionsFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('headerRowNumber', NumberType::class, [
            'label' => /* @Desc("Header row number") */ 'csv_reader.form.options.headerRowNumber.label',
        ]);
        $builder->add('delimiter', ChoiceType::class, [
            'label' => /* @Desc("Delimiter") */ 'csv_reader.form.options.delimiter.label',
            'choices' => array_combine(CsvFileReaderOptions::DELIMITERS, CsvFileReaderOptions::DELIMITERS),
        ]);
        $builder->add('enclosure', ChoiceType::class, [
            'label' => /* @Desc("Enclosure") */ 'csv_reader.form.options.enclosure.label',
            'choices' => array_combine(CsvFileReaderOptions::ENCLOSURE, CsvFileReaderOptions::ENCLOSURE),
        ]);
        $builder->add('escape', ChoiceType::class, [
            'label' => /* @Desc("Escape") */ 'csv_reader.form.options.escape.label',
            'choices' => array_combine(CsvFileReaderOptions::ESCAPE, CsvFileReaderOptions::ESCAPE),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                                   'data_class' => CsvFileReader::getOptionsType(),
                                   'translation_domain' => 'forms',
                                ]);
    }
}
