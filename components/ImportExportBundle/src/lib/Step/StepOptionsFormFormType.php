<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptionsFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class StepOptionsFormFormType extends ComponentOptionsFormType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => AbstractStep::getOptionsType(),
            'translation_domain' => 'forms',
        ]);
    }
}
