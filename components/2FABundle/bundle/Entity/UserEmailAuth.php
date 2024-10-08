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
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

final class UserEmailAuth extends User implements TwoFactorInterface
{
    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $authCode;

    public function __construct(APIUser $user, array $roles = [])
    {
        parent::__construct($user, $roles);

        $this->email = $user->email;
    }

    public function isEmailAuthEnabled(): bool
    {
        return true;
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function __serialize(): array
    {
        return [
            'reference' => $this->getAPIUserReference(),
            'roles' => $this->getRoles(),
            'email' => $this->getEmailAuthRecipient(),
            'authCode' => $this->getEmailAuthCode(),
        ];
    }
}
