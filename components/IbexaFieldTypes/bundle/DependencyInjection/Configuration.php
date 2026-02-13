<?php

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\Configuration as SAConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SAConfiguration
{
    public const CONFIG_RESOLVER_NAMESPACE = 'acx_field_types';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::CONFIG_RESOLVER_NAMESPACE);

        $rootNode = $treeBuilder->getRootNode();

        $this->generateScopeBaseNode($rootNode)
            ->arrayNode('acx_selection')->info('Choices entries')
                ->prototype('array')
                ->useAttributeAsKey('name')
                    ->prototype('variable')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
