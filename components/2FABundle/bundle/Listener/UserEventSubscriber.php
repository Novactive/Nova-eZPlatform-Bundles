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

use eZ\Publish\API\Repository\Events\User\DeleteUserEvent;
use Novactive\Bundle\eZ2FABundle\Core\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DeleteUserEvent::class => 'onDeleteUser',
        ];
    }

    public function onDeleteUser(DeleteUserEvent $event): void
    {
        $this->userRepository->deleteUserAuthSecret($event->getUser()->id);
    }
}
