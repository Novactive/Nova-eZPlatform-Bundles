<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Xls;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\FileReaderOptionsFormType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class XlsReaderOptionsFormType extends FileReaderOptionsFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('tabName', TextType::class, [
            'label' => /* @Desc("Tab name") */ 'xls_reader.form.options.tabName.label',
        ]);
        $builder->add('headerRowNumber', NumberType::class, [
            'label' => /* @Desc("Header row number") */ 'xls_reader.form.options.headerRowNumber.label',
        ]);
        $rangeForm = $builder->create('colsRange', FormType::class, [
            'label' => /* @Desc("Columns range") */ 'xls_reader.form.options.colsRange.label',
        ]);
        $builder->add($rangeForm);
        $rangeForm->add('from', TextType::class, [
            'label' => /* @Desc("Start") */ 'xls_reader.form.options.colsRange.start.label',
        ]);
        $rangeForm->add('to', TextType::class, [
            'label' => /* @Desc("End") */ 'xls_reader.form.options.colsRange.end.label',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => XlsReader::getOptionsType(),
            'translation_domain' => 'forms',
        ]);
    }
}
