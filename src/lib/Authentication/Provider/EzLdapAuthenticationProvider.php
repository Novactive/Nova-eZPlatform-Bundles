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
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\Authentication\Provider\LdapBindAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\ChainUserProvider;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class EzLdapAuthenticationProvider extends LdapBindAuthenticationProvider
{
    /** @var LoggerInterface */
    protected $logger;
    /** @var UserProviderInterface */
    private $userProvider;

    /**
     * @inheritDoc
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
            $dnString   = $authConfig['dn_string'] ?? $dnString;
            $this->setQueryString($authConfig['query_string']);
        }
        $this->userProvider = $userProvider;
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
     * @inheritDoc
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token): void
    {
        parent::checkAuthentication($user, $token);
        $eZLdapUserProvider = $this->getEzLdapUserProvider([$this->userProvider]);

        if ($user instanceof EzLdapUser && $eZLdapUserProvider) {
            try {
                $eZLdapUserProvider->checkEzUser($user);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
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
