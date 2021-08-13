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
    /**
     * @var SiteAccessAwareQueryExecutor
     */
    private $queryExecutor;

    public function __construct(SiteAccessAwareQueryExecutor $queryExecutor)
    {
        $this->queryExecutor = $queryExecutor;
    }

    public function insertUserGoogleAuthSecret(int $userId, string $secret): void
    {
        $query = <<<QUERY
                INSERT INTO user_google_auth_secret (user_contentobject_id, google_authentication_secret) 
                VALUES (?, ?)
            QUERY;
        ($this->queryExecutor)($query, [$userId, $secret], [PDO::PARAM_INT, PDO::PARAM_STR]);
    }

    public function deleteUserGoogleAuthSecret(int $userId): void
    {
        $query = <<<QUERY
                DELETE FROM user_google_auth_secret
                WHERE user_contentobject_id = ? 
            QUERY;
        ($this->queryExecutor)($query, [$userId], [PDO::PARAM_INT]);
    }

    public function getUserGoogleAuthSecretByUserId(int $userId)
    {
        $query = <<<QUERY
                SELECT google_authentication_secret as secret 
                FROM user_google_auth_secret
                WHERE user_contentobject_id = ?
                LIMIT 1
            QUERY;

        return ($this->queryExecutor)($query, [$userId], [PDO::PARAM_INT])->fetchAssociative();
    }
}
