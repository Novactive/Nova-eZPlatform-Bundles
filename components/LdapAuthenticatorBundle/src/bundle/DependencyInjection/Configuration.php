<?php

/**
 * NovaeZLDAPAuthenticator Bundle.
 *
 * @package   Novactive\Bundle\eZLDAPAuthenticatorBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZLdapAuthenticatorBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZLDAPAuthenticatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const DEFAULT_PORT = 389;
    public const DEFAULT_VERSION = 3;

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder
            ->root('nova_ez_ldap')
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('ldap')
                                ->children()
                                    ->arrayNode('adapter')
                                        ->children()
                                            ->scalarNode('connection_string')->end()
                                            ->scalarNode('host')->defaultValue('localhost')->end()
                                            ->scalarNode('port')->defaultValue(self::DEFAULT_PORT)->end()
                                            ->scalarNode('version')->defaultValue(self::DEFAULT_VERSION)->end()
                                            ->enumNode('encryption')
                                                ->defaultValue('none')
                                                ->values(['none', 'ssl', 'tls'])
                                            ->end()
                                            ->arrayNode('options')
                                                ->prototype('scalar')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('user_provider')
                                        ->children()
                                            ->scalarNode('base_dn')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('search_dn')->end()
                                            ->scalarNode('search_password')->end()
                                            ->scalarNode('uid_key')->defaultValue('sAMAccountName')->end()
                                            ->scalarNode('filter')->defaultValue('({uid_key}={username})')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('ezuser')
                                ->children()
                                    ->integerNode('admin_user_id')->isRequired()->end()
                                    ->integerNode('user_group_id')->isRequired()->end()
                                    ->scalarNode('email_attr')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                        ->info('eZPublish requres email to create user')
                                    ->end()
                                    ->arrayNode('attributes')
                                        ->defaultValue([])
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
        //                ->arrayNode('default_roles')
        //                    ->defaultValue(['ROLE_USER'])
        //                    ->prototype('scalar')->cannotBeEmpty()->end()
        //                ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
