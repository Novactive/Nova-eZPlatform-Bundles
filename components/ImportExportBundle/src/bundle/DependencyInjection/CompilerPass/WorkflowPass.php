<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\DependencyInjection\CompilerPass;

use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WorkflowPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $workflowServicesMap = [];
        $availableWorkflowServices = [];
        foreach (array_keys($container->findTaggedServiceIds('almaviacx.import_export.workflow')) as $serviceId) {
            $workflowServiceDefinition = $container->getDefinition($serviceId);
            $workflowServiceDefinition->setShared(false);
            $workflowServiceClassName = $workflowServiceDefinition->getClass();
            $workflowServiceDefaultConfiguration = WorkflowRegistry::getWorkflowDefaultConfiguration(
                $workflowServiceClassName
            );

            $workflowIdentifier = $workflowServiceDefaultConfiguration->getIdentifier();
            $workflowServicesMap[$workflowIdentifier] = new Reference($serviceId);
            $availableWorkflowServices[$workflowIdentifier] = $workflowServiceClassName;
        }

        $serviceLocator = ServiceLocatorTagPass::register($container, $workflowServicesMap);

        $registryDefinition = $container->getDefinition(WorkflowRegistry::class);
        $registryDefinition->replaceArgument('$typeContainer', $serviceLocator);
        $registryDefinition->replaceArgument('$availableWorkflowServices', $availableWorkflowServices);
    }
}
