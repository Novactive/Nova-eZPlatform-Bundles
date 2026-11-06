<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\CsvIterator;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptionsFormType;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\Csv\CsvFileReaderOptions;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvIteratorOptionsFormType extends ProcessorOptionsFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('source', TextType::class, [
            'label' => /* @Desc("Source") */ 'csv_iterator.form.options.source.label',
        ]);
        $builder->add('headerRowNumber', NumberType::class, [
            'label' => /* @Desc("Header row number") */ 'csv_iterator.form.options.headerRowNumber.label',
        ]);
        $builder->add('delimiter', ChoiceType::class, [
            'label' => /* @Desc("Delimiter") */ 'csv_iterator.form.options.delimiter.label',
            'choices' => array_combine(CsvFileReaderOptions::DELIMITERS, CsvFileReaderOptions::DELIMITERS),
        ]);
        $builder->add('enclosure', ChoiceType::class, [
            'label' => /* @Desc("Enclosure") */ 'csv_iterator.form.options.enclosure.label',
            'choices' => array_combine(CsvFileReaderOptions::ENCLOSURE, CsvFileReaderOptions::ENCLOSURE),
        ]);
        $builder->add('escape', ChoiceType::class, [
            'label' => /* @Desc("Escape") */ 'csv_iterator.form.options.escape.label',
            'choices' => array_combine(CsvFileReaderOptions::ESCAPE, CsvFileReaderOptions::ESCAPE),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                                   'data_class' => CsvIterator::getOptionsType(),
                                   'translation_domain' => 'forms',
                               ]);
    }
}
