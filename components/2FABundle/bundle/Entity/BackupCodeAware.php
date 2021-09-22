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

trait BackupCodeAware
{
    /**
     * @var array
     */
    private $backupCodes = [];

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
}
