<?php
/**
 * NovaeZLDAPAuthenticator Bundle.
 *
 * @package   Novactive\Bundle\eZLDAPAuthenticator
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZLdapAuthenticatorBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\eZLDAPAuthenticator\User;

use Psr\Log\LoggerInterface;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\User\LdapUserProvider;

class NovaEzLdapUserProvider extends LdapUserProvider
{
    public function __construct(LdapInterface $ldap, array $config, LoggerInterface $logger)
    {
        parent::__construct(
            $ldap,
            $config['ldap']['base_dn'],
            $config['ldap']['search']['search_dn'],
            $config['ldap']['search']['search_password'],
            $config['default_roles'],
            $config['ldap']['search']['uid_key'],
            '({uid_key}={username})',
            $config['ldap']['search']['password_attribute']
        );
    }
}
