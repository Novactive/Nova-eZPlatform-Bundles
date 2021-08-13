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

namespace Novactive\Bundle\eZLDAPAuthenticatorBundle\DependencyInjection\Compiler;

use Novactive\eZLDAPAuthenticator\Authentication\Provider\EzLdapAuthenticationProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SecurityPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('security.authentication.provider.ldap_bind')) {
            return;
        }

        // Override the ldap bind authentication provider.
        // We need it to inject the eZUser into the UserInterface when needed
        $daoAuthenticationProviderDef = $container->findDefinition('security.authentication.provider.ldap_bind');
        $daoAuthenticationProviderDef->setClass(EzLdapAuthenticationProvider::class);
        $daoAuthenticationProviderDef->setArgument('$ldapConnection', new Reference('nova_ez.ldap.connection'));
        $daoAuthenticationProviderDef->addMethodCall('setLogger', [new Reference('monolog.logger')]);
    }
}
