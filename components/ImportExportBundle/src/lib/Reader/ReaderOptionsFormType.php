<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptionsFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ReaderOptionsFormType extends ComponentOptionsFormType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => AbstractReader::getOptionsType(),
            'translation_domain' => 'forms',
        ]);
    }
}
