<?php

/**
 * Novactive eZ Cloudinary Bundle.
 *
 * @package   Novactive\Bundle\eZCloudinary
 *
 * @author    Novactive <novacloudinarybundle@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZCloudinaryBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZCloudinaryBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SAConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SAConfiguration
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nova_ezcloudinary');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('authentification')
                    ->children()
                        ->scalarNode('cloud_name')->defaultValue('demo')->end()
                        ->scalarNode('api_key')->defaultNull()->end()
                        ->scalarNode('api_secret')->defaultNull()->end()
                    ->end()
                ->end();

        $this->generateScopeBaseNode($rootNode)
            ->scalarNode('cloudinary_disabled')->defaultFalse()->end()
            ->scalarNode('cloudinary_fallback_variation')->defaultNull()->end()
             ->arrayNode('cloudinary_fecth_proxy')
                ->children()
                    ->scalarNode('host')->defaultNull()->end()
                    ->scalarNode('port')->defaultNull()->end()
                ->end()
            ->end()
             ->arrayNode('cloudinary_variations')
                ->prototype('array')
                    ->children()
                        ->scalarNode('ezreference_variation')->defaultNull()->end()
                        ->arrayNode('filters')
                            ->prototype('variable')

                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        $rootNode->end();

        return $treeBuilder;
    }
}
