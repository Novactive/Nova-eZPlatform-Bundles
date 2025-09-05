<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use Psr\Container\ContainerInterface;
use Symfony\Component\VarExporter\Instantiator;

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

    public static function getWorkflowDefaultConfiguration(string $workflowServiceClassName): ?WorkflowConfiguration
    {
        try {
            $workflowService = static::getWorkflowService($workflowServiceClassName);

            $instance = Instantiator::instantiate($workflowServiceClassName);

            return $workflowService->getMethod('getDefaultConfig')->invoke($instance);
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
     * @return \ReflectionClass<\AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface>
     */
    protected static function getWorkflowService(string $workflowServiceClassName): \ReflectionClass
    {
        return new \ReflectionClass($workflowServiceClassName);
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
