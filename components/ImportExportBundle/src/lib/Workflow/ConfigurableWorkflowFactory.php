<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentRegistry;

class ConfigurableWorkflowFactory
{
    protected ComponentRegistry $componentRegistry;

    public function build(WorkflowConfiguration $workflowConfiguration): ConfigurableWorkflow
    {
        $configuration = $workflowConfiguration->getProcessConfiguration();

        return new ConfigurableWorkflow(
            $workflowConfiguration,
            $this->getReader($configuration['reader']['identifier']),
            $this->getWriters(
                array_map(function (array $writerConfiguration) {
                    return $writerConfiguration['identifier'];
                }, $configuration['writers'])
            ),
            $this->getSteps(array_map(function (array $writerConfiguration) {
                return $writerConfiguration['identifier'];
            }, $configuration['steps'])),
        );
    }
}
