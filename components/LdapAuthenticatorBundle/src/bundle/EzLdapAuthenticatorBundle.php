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

namespace Novactive\Bundle\eZLDAPAuthenticatorBundle;

use Novactive\Bundle\eZLDAPAuthenticatorBundle\DependencyInjection\Compiler\SecurityPass;
use Novactive\Bundle\eZLDAPAuthenticatorBundle\DependencyInjection\EzLdapAuthenticatorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzLdapAuthenticatorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): EzLdapAuthenticatorExtension
    {
        return new EzLdapAuthenticatorExtension();
    }

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new SecurityPass());
    }
}
