<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\DependencyInjection\CompilerPass;

use Ibexa\Solr\Container\Compiler\GatewayRegistryPass;
use Novactive\EzSolrSearchExtra\Api\Gateway;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GatewayCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $alias = $this->getServicePrefix();
        $gateways = $container->findTaggedServiceIds(GatewayRegistryPass::GATEWAY_SERVICE_TAG);
        foreach ($gateways as $serviceId => $tags) {
            $connectionName = $tags[0]['connection'];
            $nativeGatewayDefiniton = $container->getDefinition($serviceId);

            $gatewayDefinition = new ChildDefinition(Gateway::class);
            $gatewayDefinition->setArgument('$endpointResolver', $nativeGatewayDefiniton->getArgument('$endpointResolver'));
            $gatewayDefinition->setArgument('$distributionStrategy', $nativeGatewayDefiniton->getArgument('$distributionStrategy'));

            $gatewayId = "$alias.connection.$connectionName.gateway_id";
            $container->setDefinition($gatewayId, $gatewayDefinition);
        }
    }

    private function getServicePrefix(): string
    {
        return 'nova.solr';
    }
}
