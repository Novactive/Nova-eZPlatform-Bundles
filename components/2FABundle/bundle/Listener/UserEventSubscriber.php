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

namespace Novactive\Bundle\eZ2FABundle\Listener;

use eZ\Publish\API\Repository\Events\User\CreateUserEvent;
use eZ\Publish\API\Repository\Events\User\DeleteUserEvent;
use Novactive\Bundle\eZ2FABundle\Core\SiteAccessAwareQueryExecutor;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var GoogleAuthenticator
     */
    private $googleAuthenticator;

    /**
     * @var SiteAccessAwareQueryExecutor
     */
    private $queryExecutor;

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
        $this->queryExecutor->insertUserGoogleAuthSecret(
            $event->getUser()->id,
            $this->googleAuthenticator->generateSecret()
        );
    }

    public function onDeleteUser(DeleteUserEvent $event): void
    {
        $this->queryExecutor->deleteUserGoogleAuthSecret($event->getUser()->id);
    }
}
