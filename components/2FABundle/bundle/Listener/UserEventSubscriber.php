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

namespace Novactive\Bundle\eZ2FABundle\Listener;

use Novactive\Bundle\eZ2FABundle\Core\SiteAccessAwareQueryExecutor;
use eZ\Publish\API\Repository\Events\User\CreateUserEvent;
use eZ\Publish\API\Repository\Events\User\DeleteUserEvent;
use PDO;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserEventSubscriber implements EventSubscriberInterface
{
    private GoogleAuthenticator $googleAuthenticator;

    private SiteAccessAwareQueryExecutor $queryExecutor;

    public function __construct(SiteAccessAwareQueryExecutor $queryExecutor, GoogleAuthenticator $googleAuthenticator)
    {
        $this->googleAuthenticator = $googleAuthenticator;
        $this->queryExecutor = $queryExecutor;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreateUserEvent::class => 'onCreateUser',
            DeleteUserEvent::class => 'onDeleteUser',
        ];
    }

    public function onCreateUser(CreateUserEvent $event): void
    {
        $userId = $event->getUser()->id;
        $secret = $this->googleAuthenticator->generateSecret();

        $query = <<<QUERY
                INSERT INTO user_google_auth_secret (user_contentobject_id, google_authentication_secret) 
                VALUES (?, ?)
            QUERY;
        ($this->queryExecutor)($query, [$userId, $secret], [PDO::PARAM_INT, PDO::PARAM_STR]);
    }

    public function onDeleteUser(DeleteUserEvent $event): void
    {
        $userId = $event->getUser()->id;
        $query = <<<QUERY
                DELETE FROM user_google_auth_secret
                WHERE user_contentobject_id = ? 
            QUERY;
        ($this->queryExecutor)($query, [$userId], [PDO::PARAM_INT]);
    }
}
