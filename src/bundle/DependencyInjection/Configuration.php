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
    const DEFAULT_PORT    = 389;
    const DEFAULT_VERSION = 3;

    public function getConfigTreeBuilder(): TreeBuilder
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
                                ->integerNode('port')->defaultValue(self::DEFAULT_PORT)->end()
                                ->integerNode('version')->defaultValue(self::DEFAULT_VERSION)->end()
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
