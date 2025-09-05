<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\File;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptionsFormType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileReaderOptionsFormType extends ReaderOptionsFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('file', FileType::class, [
            'label' => /* @Desc("File") */ 'file_reader.form.options.file.label',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => AbstractFileReader::getOptionsType(),
            'translation_domain' => 'forms',
        ]);
    }
}
