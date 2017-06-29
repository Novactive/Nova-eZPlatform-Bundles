<?php
/**
 * Novactive eZ Cloudinary Bundle
 *
 * @package   Novactive\Bundle\eZCloudinary
 * @author    Novactive <novacloudinarybundle@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZCloudinaryBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZCloudinaryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;

/**
 * Class Configuration
 */
class Configuration extends SiteAccessConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nova_ezcloudinary');
        $rootNode
            ->children()
                ->arrayNode('authentification')
                    ->children()
                        ->scalarNode('cloud_name')->defaultValue('demo')->end()
                        ->scalarNode('api_key')->defaultNull()->end()
                        ->scalarNode('api_secret')->defaultNull()->end()
                    ->end()
                ->end();
                $this->generateScopeBaseNode( $rootNode )
                     ->arrayNode( 'cloudinary_fecth_proxy' )
                        ->children()
                            ->scalarNode('host')->defaultNull()->end()
                            ->scalarNode('port')->defaultNull()->end()
                        ->end()
                    ->end()
                     ->arrayNode( 'cloudinary_variations' )
                        ->prototype('array')
                            ->children()
                                ->scalarNode('ezreference_variation')->defaultNull()->end()
                                ->arrayNode( 'filters' )
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
