<?php

/**
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Maxim Strukov <maxim.strukov@almaviacx.com>
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle\Core;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Novactive\Bundle\eZ2FABundle\DependencyInjection\Configuration;
use Novactive\Bundle\eZ2FABundle\Entity\UserGoogleAuthSecret;
use Novactive\Bundle\eZ2FABundle\Entity\UserTotpAuthSecret;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticator;

final class SiteAccessAwareAuthenticatorResolver implements SiteAccessAware
{
    /**
     * @var SiteAccess|null
     */
    private $siteAccess;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $config;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var GoogleAuthenticator
     */
    private $googleAuthenticator;

    /**
     * @var TotpAuthenticator
     */
    private $totpAuthenticator;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        ConfigResolverInterface $configResolver,
        GoogleAuthenticator $googleAuthenticator,
        TotpAuthenticator $totpAuthenticator,
        UserRepository $userRepository
    ) {
        $this->configResolver = $configResolver;
        $this->googleAuthenticator = $googleAuthenticator;
        $this->totpAuthenticator = $totpAuthenticator;
        $this->userRepository = $userRepository;
    }

    /**
     * @required
     */
    public function setSiteAccess(SiteAccess $siteAccess = null): void
    {
        $this->siteAccess = $siteAccess;
        $this->setConfig();
    }

    private function setConfig(): void
    {
        $this->method = $this->configResolver->getParameter(
            '2fa_method',
            Configuration::NAMESPACE,
            $this->siteAccess->name
        );
        $this->config = $this->configResolver->getParameter(
            'config',
            Configuration::NAMESPACE,
            $this->siteAccess->name
        );
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUserAuthenticatorEntity(User $user)
    {
        if ('google' === $this->method) {
            return new UserGoogleAuthSecret($user->getAPIUser(), $user->getRoles());
        }
        if ('microsoft' === $this->method) {
            return new UserTotpAuthSecret($user->getAPIUser(), $user->getRoles());
        }

        return new UserTotpAuthSecret($user->getAPIUser(), $user->getRoles(), null, $this->config);
    }

    public function getUserForDecorator(User $user): User
    {
        $userSecrets = $this->getUserSecrets($user);
        if (false === $userSecrets || empty($userSecrets["{$this->method}_authentication_secret"])) {
            return $user;
        }

        $authenticatorEntity = $this->getUserAuthenticatorEntity($user);
        $authenticatorEntity->setAuthenticatorSecret($userSecrets["{$this->method}_authentication_secret"]);

        return $authenticatorEntity;
    }

    public function getAuthenticator()
    {
        if ('google' === $this->method) {
            return $this->googleAuthenticator;
        }

        return $this->totpAuthenticator;
    }

    public function validateCodeAndUpdateUser(User $user, array $formData): bool
    {
        /* @var UserGoogleAuthSecret|UserTotpAuthSecret $user */
        $user->setAuthenticatorSecret($formData['secretKey']);
        if ($this->getAuthenticator()->checkCode($user, $formData['code'])) {
            $this->userRepository->insertUpdateUserAuthSecret(
                $user->getAPIUser()->id,
                $formData['secretKey'],
                $this->method
            );

            return true;
        }

        return false;
    }

    public function checkIfUserSecretExists(User $user): bool
    {
        $userAuthSecrets = $this->getUserSecrets($user);

        return is_array($userAuthSecrets) &&
               (
                   !empty($userAuthSecrets['google_authentication_secret']) ||
                   !empty($userAuthSecrets['totp_authentication_secret']) ||
                   !empty($userAuthSecrets['microsoft_authentication_secret'])
               );
    }

    public function getUserSecrets(User $user)
    {
        return $this->userRepository->getUserAuthSecretByUserId($user->getAPIUser()->id);
    }

    public function deleteUserAuthSecret(User $user): void
    {
        $this->userRepository->deleteUserAuthSecret($user->getAPIUser()->id, $this->method);
    }
}
