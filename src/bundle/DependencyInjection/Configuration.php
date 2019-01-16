<?php

namespace Novactive\EzLdapAuthenticatorBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder
            ->root('nova_ez_ldap')
            ->children()
                ->scalarNode('host')->defaultValue('localhost')->end()
                ->integerNode('port')->defaultValue(389)->end()
                ->integerNode('version')->defaultValue(3)->end()
                ->scalarNode('base_dn')->cannotBeEmpty()->end()
                ->scalarNode('search_dn')->cannotBeEmpty()->end()
                ->scalarNode('search_password')->cannotBeEmpty()->end()
                ->scalarNode('password_attribute')->defaultNull()->end()
                ->scalarNode('uid_key')->defaultValue('uid')->end()
                ->scalarNode('query_string')->defaultValue('({uid_key}={username})')->end()
                ->scalarNode('target_usergroup')->cannotBeEmpty()->end()
                ->arrayNode('default_roles')
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }
}