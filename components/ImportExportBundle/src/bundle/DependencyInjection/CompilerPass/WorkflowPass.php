<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\DependencyInjection\CompilerPass;

use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use RuntimeException;
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
        $taggedServiceIds = $container->findTaggedServiceIds('almaviacx.import_export.workflow');
        foreach ($taggedServiceIds as $serviceId => $tags) {
            $workflowServiceDefinition = $container->getDefinition($serviceId);
            $workflowServiceDefinition->setShared(false);
            $workflowServiceClassName = $workflowServiceDefinition->getClass();

            foreach ($tags as $tag) {
                if (!$tag['identifier']) {
                    throw new RuntimeException(
                        'The "identifier" attribute is required for the "almaviacx.import_export.workflow" tag.'
                    );
                }
                $workflowServicesMap[$tag['identifier']] = new Reference($serviceId);
                $availableWorkflowServices[$tag['identifier']] = $workflowServiceClassName;
            }
        }

        $serviceLocator = ServiceLocatorTagPass::register($container, $workflowServicesMap);

        $registryDefinition = $container->getDefinition(WorkflowRegistry::class);
        $registryDefinition->replaceArgument('$typeContainer', $serviceLocator);
        $registryDefinition->replaceArgument('$availableWorkflowServices', $availableWorkflowServices);
    }
}
