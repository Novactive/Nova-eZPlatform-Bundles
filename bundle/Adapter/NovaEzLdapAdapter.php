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

namespace Novactive\Bundle\eZLDAPAuthenticatorBundle\Adapter;

use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;

class NovaEzLdapAdapter extends Adapter
{
    public function __construct(array $config)
    {
        parent::__construct($config['ldap']['connection']);
    }
}
