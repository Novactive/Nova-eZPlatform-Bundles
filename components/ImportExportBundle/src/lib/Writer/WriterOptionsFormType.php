<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptionsFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class WriterOptionsFormType extends ComponentOptionsFormType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                                    'data_class' => AbstractWriter::getOptionsType(),
                                    'translation_domain' => 'forms',
                                ]);
    }
}
