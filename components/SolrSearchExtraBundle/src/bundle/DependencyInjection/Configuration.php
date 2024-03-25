<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration extends SiteAccessAware\Configuration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ez_solr_search_extra');
        $rootNode = $treeBuilder->getRootNode();
        $systemNode = $this->generateScopeBaseNode($rootNode);
        $systemNode
                ->arrayNode('fulltext_fields')
                    ->info('List of fulltext fields')
                    ->example(
                        [
                            'title' => [
                                'title',
                                'article/name',
                            ],
                        ]
                    )
                    ->useAttributeAsKey('field_name')
                    ->requiresAtLeastOneElement()
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
                ->arrayNode('custom_fields')
                    ->info('List of custom fields')
                    ->example(
                        [
                            'title' => [
                                'title',
                                'article/name',
                            ],
                        ]
                    )
                    ->useAttributeAsKey('field_name')
                    ->requiresAtLeastOneElement()
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
                ->arrayNode('publishdate_fields')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
        ->end();

        return $treeBuilder;
    }
}
