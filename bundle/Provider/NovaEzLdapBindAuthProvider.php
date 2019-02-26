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

namespace Novactive\Bundle\eZLDAPAuthenticatorBundle\Provider;

use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\Authentication\Provider\LdapBindAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class NovaEzLdapBindAuthProvider extends LdapBindAuthenticationProvider
{
    public function __construct(
        UserProviderInterface $userProvider,
        UserCheckerInterface $userChecker,
        string $providerKey,
        LdapInterface $ldap,
        bool $hideUserNotFoundExceptions = true,
        array $config = []
    ) {
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
