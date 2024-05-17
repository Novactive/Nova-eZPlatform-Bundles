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

use PDO;

/**
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
final class UserRepository
{
    public function __construct(private SiteAccessAwareQueryExecutor $queryExecutor)
    {
    }

    public function insertUpdateUserAuthSecret(int $userId, string $secret, string $prefix, string $backupCodes): void
    {
        if (is_array($this->getUserAuthData($userId))) {
            $query = <<<QUERY
                UPDATE user_auth_secret
                SET {$prefix}_authentication_secret = ?, backup_codes = ?
                WHERE user_contentobject_id = ? 
            QUERY;
            ($this->queryExecutor)(
                $query,
                [$secret, $backupCodes, $userId],
                [PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_INT]
            );
        } else {
            $query = <<<QUERY
                INSERT INTO user_auth_secret (user_contentobject_id, {$prefix}_authentication_secret, backup_codes) 
                VALUES (?, ?, ?)
            QUERY;
            ($this->queryExecutor)(
                $query,
                [$userId, $secret, $backupCodes],
                [PDO::PARAM_INT, PDO::PARAM_STR, PDO::PARAM_STR]
            );
        }
    }

    public function deleteUserAuthData(int $userId): void
    {
        $query = <<<QUERY
                DELETE FROM user_auth_secret
                WHERE user_contentobject_id = ? 
            QUERY;
        ($this->queryExecutor)($query, [$userId], [PDO::PARAM_INT]);
    }

    public function deleteUserAuthSecretAndEmail(int $userId, ?string $prefix): void
    {
        $emptySecret = (null === $prefix || 'email' === $prefix) ? '' : "{$prefix}_authentication_secret = '', ";

        $query = <<<QUERY
                UPDATE user_auth_secret
                SET {$emptySecret} backup_codes = '', email_authentication = 0, email_authentication_code = ''
                WHERE user_contentobject_id = ? 
            QUERY;
        ($this->queryExecutor)($query, [$userId], [PDO::PARAM_INT]);
    }

    public function getUserAuthData(int $userId)
    {
        $query = <<<QUERY
                SELECT *
                FROM user_auth_secret
                WHERE user_contentobject_id = ?
                LIMIT 1
            QUERY;

        return ($this->queryExecutor)($query, [$userId], [PDO::PARAM_INT])->fetchAssociative();
    }

    public function updateBackupCodes(int $userId, string $backupCodes): void
    {
        $query = <<<QUERY
                UPDATE user_auth_secret
                SET backup_codes = ?
                WHERE user_contentobject_id = ? 
            QUERY;
        ($this->queryExecutor)($query, [$backupCodes, $userId], [PDO::PARAM_STR, PDO::PARAM_INT]);
    }

    public function insertUpdateEmailAuthentication(int $userId): void
    {
        if (is_array($this->getUserAuthData($userId))) {
            $query = <<<QUERY
                UPDATE user_auth_secret
                SET email_authentication = 1
                WHERE user_contentobject_id = ? 
            QUERY;
            ($this->queryExecutor)($query, [$userId], [PDO::PARAM_INT]);
        } else {
            $query = <<<QUERY
                INSERT INTO user_auth_secret (user_contentobject_id, email_authentication) 
                VALUES (?, 1)
            QUERY;
            ($this->queryExecutor)(
                $query,
                [$userId],
                [PDO::PARAM_INT]
            );
        }
    }

    public function updateEmailAuthenticationCode(int $userId, string $authCode): void
    {
        $query = <<<QUERY
                UPDATE user_auth_secret
                SET email_authentication_code = ?
                WHERE user_contentobject_id = ? 
            QUERY;
        ($this->queryExecutor)($query, [$authCode, $userId], [PDO::PARAM_STR, PDO::PARAM_INT]);
    }
}
