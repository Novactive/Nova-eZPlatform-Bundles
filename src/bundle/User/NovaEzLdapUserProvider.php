<?php

namespace Novactive\EzLdapAuthenticatorBundle\User;

use Psr\Log\LoggerInterface;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\User\LdapUserProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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