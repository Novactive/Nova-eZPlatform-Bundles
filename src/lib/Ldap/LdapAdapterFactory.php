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

namespace Novactive\eZLDAPAuthenticator\Ldap;

use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;

class LdapAdapterFactory
{
    public function createAdapter(array $options)
    {
        return new Adapter($options);
    }
}
