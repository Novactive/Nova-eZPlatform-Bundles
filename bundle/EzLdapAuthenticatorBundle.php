<?php
/**
 * NovaeZLdapAuthenticatorBundle.
 *
 * @package   NovaeZLdapAuthenticatorBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZLdapAuthenticatorBundle/blob/master/LICENSE
 */

namespace Novactive\EzLdapAuthenticatorBundle;

use Novactive\Bundle\eZLDAPAuthenticatorBundle\DependencyInjection\EzLdapAuthenticatorExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzLdapAuthenticatorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new EzLdapAuthenticatorExtension();
    }
}
