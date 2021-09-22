<?php

/**
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Yassine HANINI
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle\Entity;

use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Scheb\TwoFactorBundle\Model\BackupCodeInterface;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

final class UserGoogleAuthSecret extends User implements TwoFactorInterface, BackupCodeInterface
{
    /**
     * @var string|null
     */
    private $secret;

    /**
     * @var array
     */
    private $backupCodes = [];

    public function __construct(APIUser $user, array $roles = [], ?string $secret = null)
    {
        parent::__construct($user, $roles);

        $this->secret = $secret;
    }

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return null !== $this->secret;
    }

    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->getUsername();
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->secret;
    }

    public function setAuthenticatorSecret(?string $googleAuthenticatorSecret): void
    {
        $this->secret = $googleAuthenticatorSecret;
    }

    public function isBackupCode(string $code): bool
    {
        return in_array((int) $code, $this->backupCodes, true);
    }

    public function invalidateBackupCode(string $code): void
    {
        $key = array_search((int) $code, $this->backupCodes, true);
        if (false !== $key) {
            unset($this->backupCodes[$key]);
        }
    }

    public function setBackupCodes(array $backupCodes): void
    {
        $this->backupCodes = $backupCodes;
    }

    public function getBackupCodes(): array
    {
        return $this->backupCodes;
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
