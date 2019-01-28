<?php

namespace Novactive\EzLdapAuthenticatorBundle\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginLdapFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NovaEzLdapLoginFactory extends FormLoginLdapFactory
{
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = 'nova_ez.ldap.bind_provider';
        $definition = $container
            ->setDefinition($provider, new ChildDefinition('security.authentication.provider.ldap_bind'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(1, new Reference('security.user_checker.'.$id))
            ->replaceArgument(2, $id)
            ->replaceArgument(3, new Reference('nova_ez.ldap'))
            ->replaceArgument(4, 'o=gouv,c=fr')
        ;

        $definition->addMethodCall('setQueryString', ['(uid={username})']);


        return $provider;
    }
}