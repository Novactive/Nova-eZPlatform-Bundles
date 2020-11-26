<?php

/**
 * Novactive eZ Fastly Image Optimizer Bundle.
 *
 * @author    Novactive <direction.technique@novactive.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZFastlyImageOptimizerBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZFastlyImageOptimizerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SAConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SAConfiguration
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nova_ezfastlyio');
        $rootNode = $treeBuilder->getRootNode();

        $this->generateScopeBaseNode($rootNode)
             ->scalarNode('fastlyio_disabled')->defaultFalse()->end()
             ->arrayNode('fastlyio_variations')
                ->prototype('array')
                    ->children()
                        ->scalarNode('ezreference_variation')->defaultValue('original')->end()
                        ->arrayNode('filters')
                            ->prototype('variable')
                            ->end()
                        ->end()
                    ->end()
                ->end()
             ->end();

        return $treeBuilder;
    }
}
