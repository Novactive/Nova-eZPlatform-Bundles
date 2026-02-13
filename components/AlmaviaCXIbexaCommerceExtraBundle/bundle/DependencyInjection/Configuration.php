<?php

namespace AlmaviaCX\Ibexa\Commerce\Extra\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration extends SiteAccessConfiguration
{
    public const CONFIGRESOLVER_NAMESPACE = 'almaviacx_ibexa_commerce_extra';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::CONFIGRESOLVER_NAMESPACE);
        $rootNode    = $treeBuilder->getRootNode();

        $this->generateScopeBaseNode($rootNode)
                ->booleanNode('named_cart_enabled')->end()
                ->scalarNode('named_cart_name')->end()
                ->variableNode('named_cart_context')->end()
                ->scalarNode('named_workflow_name')->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
