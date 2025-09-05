<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\DependencyInjection\CompilerPass;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ComponentPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $servicesMap = [];
        $tagName = 'almaviacx.import_export.component';
        foreach ($container->findTaggedServiceIds($tagName) as $serviceId => $tags) {
            $serviceDefinition = $container->getDefinition($serviceId);
            $serviceDefinition->setShared(false);
            $servicesMap[$serviceDefinition->getClass()] = new Reference($serviceId);

            foreach ($tags as $attributes) {
                if (!isset($attributes['alias'])) {
                    throw new \LogicException(
                        sprintf(
                            'Service "%s" tagged with "%s" service tag needs an "alias" attribute 
                            to identify the Component Type.',
                            $serviceId,
                            $tagName
                        )
                    );
                }
                $servicesMap[$attributes['alias']] = new Reference($serviceId);
            }
        }

        $serviceLocator = ServiceLocatorTagPass::register($container, $servicesMap);

        $registryDefinition = $container->getDefinition(ComponentRegistry::class);
        $registryDefinition->replaceArgument('$typeContainer', $serviceLocator);
    }
}
