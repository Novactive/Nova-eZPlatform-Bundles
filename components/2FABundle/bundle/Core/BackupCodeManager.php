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

use eZ\Publish\Core\MVC\Symfony\Security\User;
use Novactive\Bundle\eZ2FABundle\Entity\BackupCodeInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Backup\BackupCodeManagerInterface;

final class BackupCodeManager implements BackupCodeManagerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function isBackupCode($user, string $code): bool
    {
        if ($user instanceof BackupCodeInterface) {
            return $user->isBackupCode($code);
        }

        return false;
    }

    public function invalidateBackupCode($user, string $code): void
    {
        if ($user instanceof BackupCodeInterface) {
            /* @var User|BackupCodeInterface $user */
            $user->invalidateBackupCode($code);
            $this->userRepository->updateBackupCodes(
                $user->getAPIUser()->getUserId(),
                json_encode(array_values($user->getBackupCodes()))
            );
        }
    }
}
