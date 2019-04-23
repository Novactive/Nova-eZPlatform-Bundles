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

namespace Novactive\eZLDAPAuthenticator\User\Provider;

use Exception;
use eZ\Publish\API\Repository\Values\User\User as EzApiUser;
use Novactive\eZLDAPAuthenticator\User\Converter\LdapEntryConverter;
use Novactive\eZLDAPAuthenticator\User\EzLdapUser;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Security\Core\User\LdapUserProvider;

class EzLdapUserProvider extends LdapUserProvider
{
    /** @var LdapEntryConverter */
    protected $ldapEntryConverter;

    /**
     * @required
     */
    public function setLdapEntryConverter(LdapEntryConverter $ldapEntryConverter): void
    {
        $this->ldapEntryConverter = $ldapEntryConverter;
    }

    /**
     * @param string $username
     *
     * @throws Exception
     *
     * @return EzLdapUser|\Symfony\Component\Security\Core\User\User
     */
    protected function loadUser($username, Entry $entry)
    {
        return $this->ldapEntryConverter->convert($username, $entry);
    }

    /**
     * @throws Exception
     */
    public function checkEzUser(EzLdapUser $ezLdapUser): EzApiUser
    {
        return $this->ldapEntryConverter->convertToEzUser(
            $ezLdapUser->getUsername(),
            $ezLdapUser->getEmail(),
            $ezLdapUser->getAttributes()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return EzLdapUser::class === $class;
    }
}
