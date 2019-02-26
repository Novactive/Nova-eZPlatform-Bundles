<?php

namespace Novactive\EzLdapAuthenticatorBundle\Provider;

use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\Authentication\Provider\LdapBindAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class NovaEzLdapBindAuthProvider extends LdapBindAuthenticationProvider
{
    public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker, string $providerKey, LdapInterface $ldap, string $dnString = '{username}', bool $hideUserNotFoundExceptions = true, array $config)
    {
        parent::__construct(
            $userProvider,
            $userChecker,
            $providerKey,
            $ldap,
            $config['ldap']['search']['search_string'],
            $hideUserNotFoundExceptions
        );
    }
}