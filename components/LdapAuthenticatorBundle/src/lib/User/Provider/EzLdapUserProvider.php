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
use Psr\Log\LoggerInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\User\LdapUserProvider;

class EzLdapUserProvider extends LdapUserProvider
{
    /** @var LdapEntryConverter */
    protected $ldapEntryConverter;

    /** @var LoggerInterface */
    protected $logger;

    /** @var LdapInterface */
    protected $ldap;

    /** @var string|null */
    protected $searchDn;

    /** @var string|null */
    protected $searchPassword;

    /**
     * @param string $baseDn
     * @param string $searchDn
     * @param string $searchPassword
     * @param string $uidKey
     * @param string $filter
     * @param string $passwordAttribute
     */
    public function __construct(
        LdapInterface $ldap,
        $baseDn,
        $searchDn = null,
        $searchPassword = null,
        array $defaultRoles = [],
        $uidKey = 'sAMAccountName',
        $filter = '({uid_key}={username})',
        $passwordAttribute = null
    ) {
        parent::__construct(
            $ldap,
            $baseDn,
            $searchDn,
            $searchPassword,
            $defaultRoles,
            $uidKey,
            $filter,
            $passwordAttribute
        );
        $this->ldap = $ldap;
        $this->searchDn = $searchDn;
        $this->searchPassword = $searchPassword;
    }

    /**
     * @required
     */
    public function setLdapEntryConverter(LdapEntryConverter $ldapEntryConverter): void
    {
        $this->ldapEntryConverter = $ldapEntryConverter;
    }

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        try {
            $this->ldap->bind($this->searchDn, $this->searchPassword);
        } catch (ConnectionException $exception) {
            $message = sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
            $this->logger->critical($message, ['exception' => $exception]);
        }

        return parent::loadUserByUsername($username);
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
}
