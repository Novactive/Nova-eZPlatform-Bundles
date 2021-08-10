<?php

/*
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Yassine HANINI
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle\Security;

use Novactive\Bundle\eZ2FABundle\Core\SiteAccessAwareQueryExecutor;
use Novactive\Bundle\eZ2FABundle\Entity\UserGoogleAuthSecret;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use PDO;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class TwoFactorUserProviderDecorator implements UserProviderInterface
{
    private UserProviderInterface $provider;

    private SiteAccessAwareQueryExecutor $queryExecutor;

    public function __construct(UserProviderInterface $provider, SiteAccessAwareQueryExecutor $queryExecutor)
    {
        $this->provider = $provider;
        $this->queryExecutor = $queryExecutor;
    }

    public function loadUserByUsername(string $username)
    {
        $user = $this->provider->loadUserByUsername($username);

        if ($user instanceof User) {
            $query = <<<QUERY
                SELECT google_authentication_secret as secret 
                FROM user_google_auth_secret
                WHERE user_contentobject_id = ?
                LIMIT 1
            QUERY;
            $results = ($this->queryExecutor)($query,
                                              [$user->getAPIUserReference()->getUserId()],
                                              [PDO::PARAM_INT])->fetchAssociative();

            return new UserGoogleAuthSecret($user->getAPIUser(), $user->getRoles(), $results['secret'] ?? null);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->provider->refreshUser($user);
    }

    public function supportsClass(string $class): bool
    {
        return $this->provider->supportsClass($class);
    }
}
