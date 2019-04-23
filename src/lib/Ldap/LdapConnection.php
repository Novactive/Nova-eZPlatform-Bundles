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

use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Security\Core\User\LdapUserProvider;

class LdapConnection
{
    /** @var Ldap */
    protected $ldap;

    /** @var LdapUserProvider */
    protected $ldapUserProvider;

    /** @var array */
    protected $configs;

    /**
     * LdapConnection constructor.
     */
    public function __construct(
        Ldap $ldap,
        LdapUserProvider $ldapUserProvider,
        array $configs
    ) {
        $this->ldap             = $ldap;
        $this->ldapUserProvider = $ldapUserProvider;
        $this->configs          = $configs;
    }

    public function getLdap(): Ldap
    {
        return $this->ldap;
    }

    public function getLdapUserProvider(): LdapUserProvider
    {
        return $this->ldapUserProvider;
    }

    /**
     * @return mixed|null
     */
    public function getConfig(string $name)
    {
        return $this->configs[$name] ?? null;
    }
}
