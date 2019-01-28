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
                ->arrayNode('ldap')
                    ->children()
                        ->arrayNode('connection')
                            ->children()
                                ->scalarNode('connection_string')->defaultValue('')->end()
                                ->scalarNode('host')->defaultValue('localhost')->end()
                                ->integerNode('port')->defaultValue(389)->end()
                                ->integerNode('version')->defaultValue(3)->end()
                                ->enumNode('encryption')
                                    ->defaultValue('none')
                                    ->values(['none', 'ssl', 'tls'])
                                ->end()
                                ->arrayNode('options')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('search')
                            ->children()
                                ->scalarNode('search_dn')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('search_password')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('password_attribute')->defaultValue('userPassword')->end()
                                ->scalarNode('uid_key')->defaultValue('uid')->end()
                                ->scalarNode('search_string')->defaultValue('({uid_key}={username})')->end()
                            ->end()
                        ->end()
                        ->scalarNode('base_dn')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('ez_user')
                    ->children()
                        ->scalarNode('email_attr')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('eZPublish requres email to create user')
                        ->end()
                        ->arrayNode('attributes')
                            ->defaultValue([])
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('ldap_attr')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('user_attr')->isRequired()->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->integerNode('target_usergroup')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('default_roles')
                    ->defaultValue(['ROLE_USER'])
                    ->prototype('scalar')->cannotBeEmpty()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}