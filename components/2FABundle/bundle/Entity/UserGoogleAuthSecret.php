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

use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Ibexa\Core\MVC\Symfony\Security\User;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

final class UserGoogleAuthSecret extends User implements TwoFactorInterface, BackupCodeInterface, AuthenticatorInterface
{
    use BackupCodeAware;

    /**
     * @var string|null
     */
    private $secret;

    public function __construct(APIUser $user, array $roles = [])
    {
        parent::__construct($user, $roles);
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
