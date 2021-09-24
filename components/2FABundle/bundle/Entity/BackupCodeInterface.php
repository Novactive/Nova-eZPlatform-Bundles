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

use Scheb\TwoFactorBundle\Model\BackupCodeInterface as SchebBackupCodeInterface;

interface BackupCodeInterface extends SchebBackupCodeInterface
{
    public function setBackupCodes(array $backupCodes): void;

    public function getBackupCodes(): array;
}
