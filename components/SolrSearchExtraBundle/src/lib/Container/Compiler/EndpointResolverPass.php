<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Container\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EndpointResolverPass implements CompilerPassInterface
{
    /**
     * @throws LogicException
     */
    public function process(ContainerBuilder $container): void
    {
        $config = $container->getExtensionConfig('ibexa_solr');
        $this->processConnectionConfiguration($container, $config[0]);
    }

    /**
     * fetch for connection services definitions
     * and setting them to the container as public services.
     */
    protected function processConnectionConfiguration(ContainerBuilder $container, array $config): void
    {
        $alias = $this->getServicePrefix();
        foreach (array_keys($config['connections']) as $name) {
            $ibexaEndpointResolverAlias = "ibexa.solr.connection.$name.endpoint_resolver_id";
            if ($container->hasDefinition($ibexaEndpointResolverAlias)) {
                $novaEndpointResolverId = "$alias.connection.$name.endpoint_resolver_id";
                $endpointResolverDefinition = $container->getDefinition($ibexaEndpointResolverAlias);
                $container->setDefinition($novaEndpointResolverId, $endpointResolverDefinition)->setPublic(true);
            }
        }
    }

    private function getServicePrefix(): string
    {
        return 'nova.solr';
    }
}
