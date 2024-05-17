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

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Security\User;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Novactive\Bundle\eZ2FABundle\DependencyInjection\Configuration;
use Novactive\Bundle\eZ2FABundle\Entity\AuthenticatorInterface;
use Novactive\Bundle\eZ2FABundle\Entity\BackupCodeInterface;
use Novactive\Bundle\eZ2FABundle\Entity\UserEmailAuth;
use Novactive\Bundle\eZ2FABundle\Entity\UserGoogleAuthSecret;
use Novactive\Bundle\eZ2FABundle\Entity\UserTotpAuthSecret;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticator;

class SiteAccessAwareAuthenticatorResolver implements SiteAccessAware
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
     * @var bool
     */
    private $emailMethodEnabled;

    /**
     * @var bool
     */
    private $forceSetup;

    public function __construct(
        private ConfigResolverInterface $configResolver,
        private GoogleAuthenticator $googleAuthenticator,
        private TotpAuthenticator $totpAuthenticator,
        private UserRepository $userRepository,
        private bool $backupCodesEnabled
    ) {
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
            '2fa_mobile_method',
            Configuration::NAMESPACE,
            $this->siteAccess->name
        );
        $this->config = $this->configResolver->getParameter(
            'config',
            Configuration::NAMESPACE,
            $this->siteAccess->name
        );
        $this->emailMethodEnabled = $this->configResolver->getParameter(
            '2fa_email_method_enabled',
            Configuration::NAMESPACE,
            $this->siteAccess->name
        );
        $this->forceSetup = $this->configResolver->getParameter(
            '2fa_force_setup',
            Configuration::NAMESPACE,
            $this->siteAccess->name
        );
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function isEmailMethodEnabled(): bool
    {
        return $this->emailMethodEnabled;
    }

    public function isForceSetup(): bool
    {
        return $this->forceSetup;
    }

    public function getUserAuthenticatorEntity(User $user)
    {
        if ('email' === $this->method) {
            return new UserEmailAuth($user->getAPIUser(), $user->getRoles());
        }
        if ('google' === $this->method) {
            return new UserGoogleAuthSecret($user->getAPIUser(), $user->getRoles());
        }
        if ('microsoft' === $this->method) {
            return new UserTotpAuthSecret($user->getAPIUser(), $user->getRoles());
        }

        return new UserTotpAuthSecret($user->getAPIUser(), $user->getRoles(), $this->config);
    }

    public function getUserForDecorator(User $user): User
    {
        $userAuthData = $this->getUserAuthData($user);

        if (false === $userAuthData) {
            return $user;
        }

        if ($userAuthData['email_authentication']) {
            $this->method = 'email';
        }

        if (
            false === $userAuthData ||
            ('email' !== $this->method && empty($userAuthData["{$this->method}_authentication_secret"]))
        ) {
            return $user;
        }

        $authenticatorEntity = $this->getUserAuthenticatorEntity($user);

        if ('email' === $this->method) {
            $authenticatorEntity->setEmailAuthCode($userAuthData['email_authentication_code']);
        } else {
            $authenticatorEntity->setAuthenticatorSecret($userAuthData["{$this->method}_authentication_secret"]);
            $authenticatorEntity->setBackupCodes(json_decode($userAuthData['backup_codes']) ?? []);
        }

        return $authenticatorEntity;
    }

    public function getAuthenticator()
    {
        if ('google' === $this->method) {
            return $this->googleAuthenticator;
        }

        return $this->totpAuthenticator;
    }

    public function validateCodeAndUpdateUser(User $user, array $formData): array
    {
        /* @var User|TwoFactorInterface|AuthenticatorInterface|BackupCodeInterface $user */
        $user->setAuthenticatorSecret($formData['secretKey']);
        if ($this->getAuthenticator()->checkCode($user, $formData['code'])) {
            if ($this->backupCodesEnabled) {
                // Generating backup codes
                $backupCodes = [
                    random_int(100000, 999999),
                    random_int(100000, 999999),
                    random_int(100000, 999999),
                    random_int(100000, 999999),
                    random_int(100000, 999999),
                    random_int(100000, 999999),
                ];

                $user->setBackupCodes($backupCodes);
            }

            $this->userRepository->insertUpdateUserAuthSecret(
                $user->getAPIUser()->getUserId(),
                $formData['secretKey'],
                $this->method,
                isset($backupCodes) ? json_encode($backupCodes) : ''
            );

            return [
                'valid' => true,
                'backupCodes' => $backupCodes ?? [],
            ];
        }

        return [
            'valid' => false,
        ];
    }

    public function setEmailAuthentication(User $user): void
    {
        $this->method = 'email';
        $this->userRepository->insertUpdateEmailAuthentication($user->getAPIUser()->getUserId());
    }

    public function checkIfUserSecretOrEmailExists(User $user): bool
    {
        $userAuthData = $this->getUserAuthData($user);

        if (false === $userAuthData) {
            return false;
        }

        if ($userAuthData['email_authentication']) {
            $this->method = 'email';

            return true;
        }

        return is_array($userAuthData) &&
               (
                   !empty($userAuthData['google_authentication_secret']) ||
                   !empty($userAuthData['totp_authentication_secret']) ||
                   !empty($userAuthData['microsoft_authentication_secret'])
               );
    }

    public function getUserAuthData(User $user)
    {
        return $this->userRepository->getUserAuthData($user->getAPIUser()->getUserId());
    }

    public function deleteUserAuthSecretAndEmail(User $user): void
    {
        $this->userRepository->deleteUserAuthSecretAndEmail($user->getAPIUser()->getUserId(), $this->method);
    }
}
