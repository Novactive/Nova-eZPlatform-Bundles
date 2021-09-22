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

namespace Novactive\Bundle\eZ2FABundle\Entity;

use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfiguration;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfigurationInterface;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;

final class UserTotpAuthSecret extends User implements TwoFactorInterface, BackupCodeInterface, AuthenticatorInterface
{
    use BackupCodeAware;

    /**
     * @var string|null
     */
    private $secret;

    /**
     * @var array
     */
    private $config;

    private const DEFAULT_ALGORITHM = TotpConfiguration::ALGORITHM_SHA1;
    private const DEFAULT_PERIOD = 30;
    private const DEFAULT_DIGITS = 6;

    public function __construct(APIUser $user, array $roles = [], ?string $secret = null, array $config = [])
    {
        parent::__construct($user, $roles);

        $this->secret = $secret;
        $this->config = $config;
    }

    public function isTotpAuthenticationEnabled(): bool
    {
        return $this->secret ? true : false;
    }

    public function getTotpAuthenticationUsername(): string
    {
        return $this->getUsername();
    }

    public function getTotpAuthenticationConfiguration(): TotpConfigurationInterface
    {
        // You could persist the other configuration options in the user entity to make it individual per user.
        return new TotpConfiguration(
            $this->secret,
            $this->config['algorithm'] ?? self::DEFAULT_ALGORITHM,
            $this->config['period'] ?? self::DEFAULT_PERIOD,
            $this->config['digits'] ?? self::DEFAULT_DIGITS
        );
    }

    public function setAuthenticatorSecret(?string $totpAuthenticatorSecret): void
    {
        $this->secret = $totpAuthenticatorSecret;
    }

    public function __serialize(): array
    {
        return [
            'reference' => $this->getAPIUserReference(),
            'roles' => $this->getRoles(),
            'secret' => $this->secret,
            'backupCodes' => $this->backupCodes,
        ];
    }
}
