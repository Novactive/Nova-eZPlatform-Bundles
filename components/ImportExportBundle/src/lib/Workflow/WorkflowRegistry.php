<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use Psr\Container\ContainerInterface;
use ReflectionClass;

class WorkflowRegistry
{
    protected ContainerInterface $typeContainer;
    /** @var array<string, string> */
    protected array $availableWorkflowServices;

    /**
     * @param array<string, string> $availableWorkflowServices
     */
    public function __construct(
        ContainerInterface $typeContainer,
        array $availableWorkflowServices
    ) {
        $this->availableWorkflowServices = $availableWorkflowServices;
        $this->typeContainer = $typeContainer;
    }

    public function getWorkflow(string $identifier): WorkflowInterface
    {
        return $this->typeContainer->get($identifier);
    }

    public static function getWorkflowConfigurationFormType(string $workflowServiceClassName): ?string
    {
        try {
            $workflowService = static::getWorkflowService($workflowServiceClassName);

            return $workflowService->getMethod('getConfigurationFormType')->invoke(null);
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    public static function getWorkflowDefaultConfiguration(string $workflowServiceClassName): ?WorkflowConfiguration
    {
        try {
            $workflowService = static::getWorkflowService($workflowServiceClassName);

            return $workflowService->getMethod('getDefaultConfig')->invoke(null);
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    public function getWorkflowClassName(string $identifier): string
    {
        return $this->availableWorkflowServices[$identifier];
    }

    /**
     * @throws \ReflectionException
     *
     * @return ReflectionClass<\AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface>
     */
    protected static function getWorkflowService(string $workflowServiceClassName): ReflectionClass
    {
        return new ReflectionClass($workflowServiceClassName);
    }

    /**
     * @return array<string,string>
     */
    public function getAvailableWorkflowServices(
        int $requiredAvailability = WorkflowConfiguration::AVAILABILITY_ADMIN_UI
    ): array {
        $worflows = [];
        foreach ($this->availableWorkflowServices as $identifier => $workflowServiceClassName) {
            $baseConfig = static::getWorkflowDefaultConfiguration($workflowServiceClassName);
            if ($baseConfig && $baseConfig->isAvailable($requiredAvailability)) {
                $worflows[$identifier] = $baseConfig->getName();
            }
        }

        return $worflows;
    }
}
