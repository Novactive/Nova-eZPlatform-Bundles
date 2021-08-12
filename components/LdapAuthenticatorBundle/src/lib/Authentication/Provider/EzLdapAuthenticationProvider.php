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

namespace Novactive\eZLDAPAuthenticator\Authentication\Provider;

use Exception;
use Novactive\eZLDAPAuthenticator\Ldap\LdapConnection;
use Novactive\eZLDAPAuthenticator\User\EzLdapUser;
use Novactive\eZLDAPAuthenticator\User\Provider\EzLdapUserProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\Exception\NotBoundException;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Provider\LdapBindAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\ChainUserProvider;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class EzLdapAuthenticationProvider extends LdapBindAuthenticationProvider
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var UserProviderInterface */
    protected $userProvider;

    /** @var LdapInterface */
    protected $ldap;

    /** @var string */
    protected $dnString;

    /** @var string */
    protected $queryString;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        UserProviderInterface $userProvider,
        UserCheckerInterface $userChecker,
        string $providerKey,
        LdapInterface $ldap,
        string $dnString = '{username}',
        bool $hideUserNotFoundExceptions = true,
        LdapConnection $ldapConnection = null
    ) {
        if ($ldapConnection) {
            $authConfig = $ldapConnection->getConfig('ldap_auth');
            $dnString = $authConfig['dn_string'] ?? $dnString;
            $this->setQueryString($authConfig['query_string']);
        }
        $this->userProvider = $userProvider;
        $this->ldap = $ldap;
        $this->dnString = $dnString;
        parent::__construct(
            $userProvider,
            $userChecker,
            $providerKey,
            $ldap,
            $dnString,
            $hideUserNotFoundExceptions
        );
    }

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $queryString
     */
    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;
        parent::setQueryString($queryString);
    }

    /**
     * {@inheritdoc}
     */
    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        if (AuthenticationProviderInterface::USERNAME_NONE_PROVIDED === $username) {
            throw new UsernameNotFoundException('Username can not be null');
        }

        try {
            return $this->userProvider->loadUserByUsername($username);
        } catch (LdapException $exception) {
            $message = sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
            $this->logger->critical($message, ['exception' => $exception]);
        }

        throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
    }

    /**
     * {@inheritDoc}
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token): void
    {
        $username = $token->getUsername();
        $password = $token->getCredentials();

        if ('' === (string) $password) {
            throw new BadCredentialsException('The presented password must not be empty.');
        }

        try {
            $username = $this->ldap->escape($username, '', LdapInterface::ESCAPE_DN);

            if ($this->queryString) {
                $query = str_replace('{username}', $username, $this->queryString);
                $result = $this->ldap->query($this->dnString, $query)->execute();
                if (1 !== $result->count()) {
                    throw new BadCredentialsException('The presented username is invalid.');
                }

                $distinguishedName = $result[0]->getDn();
            } else {
                $distinguishedName = str_replace('{username}', $username, $this->dnString);
            }

            $this->ldap->bind($distinguishedName, $password);
        } catch (NotBoundException $exception) {
            $message = sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
            $this->logger->critical($message, ['exception' => $exception]);
            throw new BadCredentialsException('Connexion error.');
        } catch (ConnectionException $exception) {
            $message = sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
            $this->logger->critical($message, ['exception' => $exception]);
            throw new BadCredentialsException('The presented password is invalid.');
        }

        $eZLdapUserProvider = $this->getEzLdapUserProvider([$this->userProvider]);

        if ($user instanceof EzLdapUser && $eZLdapUserProvider) {
            try {
                $eZLdapUserProvider->checkEzUser($user);
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage(), ['exception' => $e]);
                throw new BadCredentialsException($e->getMessage());
            }
        }
    }

    protected function getEzLdapUserProvider(array $providers): ?EzLdapUserProvider
    {
        foreach ($providers as $provider) {
            if ($provider instanceof EzLdapUserProvider) {
                return $provider;
            }
            if ($provider instanceof ChainUserProvider) {
                $chainedProvider = $this->getEzLdapUserProvider($provider->getProviders());
                if ($chainedProvider) {
                    return $chainedProvider;
                }
            }
        }

        return null;
    }
}
