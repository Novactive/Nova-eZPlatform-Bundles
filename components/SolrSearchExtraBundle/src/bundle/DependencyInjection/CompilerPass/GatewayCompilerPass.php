<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\DependencyInjection\CompilerPass;

use Ibexa\Solr\Container\Compiler\GatewayRegistryPass;
use Novactive\EzSolrSearchExtra\Api\Gateway;
use Novactive\EzSolrSearchExtra\Api\GatewayRegistry;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GatewayCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $alias = $this->getServicePrefix();

        $gatewayRegistryDefinition = $container->getDefinition(GatewayRegistry::class);
        $gateways = $container->findTaggedServiceIds(GatewayRegistryPass::GATEWAY_SERVICE_TAG);
        foreach ($gateways as $serviceId => $tags) {
            $connectionName = $tags[0]['connection'];
            $nativeGatewayDefiniton = $container->getDefinition($serviceId);

            $gatewayDefinition = new ChildDefinition(Gateway::class);
            $gatewayDefinition->setArgument(
                '$endpointResolver',
                $nativeGatewayDefiniton->getArgument('$endpointResolver')
            );
            $gatewayDefinition->setArgument(
                '$distributionStrategy',
                $nativeGatewayDefiniton->getArgument('$distributionStrategy')
            );

            $gatewayId = "$alias.connection.$connectionName.gateway_id";
            $container->setDefinition($gatewayId, $gatewayDefinition);

            $gatewayRegistryDefinition->addMethodCall(
                'addGateway',
                [
                    $connectionName,
                    new Reference($gatewayId),
                ]
            );
        }
    }

    private function getServicePrefix(): string
    {
        return 'nova.solr';
    }
}
