<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use Psr\Container\ContainerInterface;

class WorkflowRegistry
{
    /**
     * @param array<string, string> $availableWorkflowServices
     */
    public function __construct(
        protected ContainerInterface $typeContainer,
        protected array $availableWorkflowServices
    ) {
    }

    public function getWorkflow(string $identifier): WorkflowInterface
    {
        return $this->typeContainer->get($identifier);
    }

    /**
     * @return array<string,string>
     */
    public function getAvailableWorkflowServices(
        int $requiredAvailability = WorkflowConfiguration::AVAILABILITY_ADMIN_UI
    ): array {
        $workflows = [];
        $availableWorkflowServicesIdentifiers = array_keys($this->availableWorkflowServices);
        foreach ($availableWorkflowServicesIdentifiers as $identifier) {
            $workflow = $this->getWorkflow($identifier);
            $baseConfig = $workflow->getDefaultConfig();
            if ($baseConfig->isAvailable($requiredAvailability)) {
                $workflows[$identifier] = $baseConfig->getName();
            }
        }

        return $workflows;
    }
}
