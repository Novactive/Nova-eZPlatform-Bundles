<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job\Form;

use AlmaviaCX\Bundle\IbexaImportExport\Job\Form\Type\JobFormType;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Form\Type\JobProcessConfigurationFormType;
use Craue\FormFlowBundle\Form\FormFlow;

class JobCreateFlow extends FormFlow
{
    /**
     * @return array<array<string, mixed>>
     */
    protected function loadStepsConfig(): array
    {
        $steps = [
            [
                'label' => 'Job configuration',
                'form_type' => JobFormType::class,
            ],
            [
                'label' => 'Components configuration',
                'form_type' => JobProcessConfigurationFormType::class,
                'form_options' => [
                    'show_initialized' => false,
                ],
            ],
        ];

        return $steps;
    }
}
