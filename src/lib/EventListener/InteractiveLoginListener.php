<?php
/**
 * NovaeZLdapAuthenticatorBundle.
 *
 * @package   NovaeZLdapAuthenticatorBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZLdapAuthenticatorBundle/blob/master/LICENSE
 */

namespace Novactive\EzLdapAuthenticator\EventListener;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\MVC\Symfony\Event\InteractiveLoginEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InteractiveLoginListener implements EventSubscriberInterface
{
    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        // This loads a generic user and assigns it back to the event.
        // You may want to create users here, or even load predefined users depending on your own rules.
        $event->setApiUser($this->userService->loadUserByLogin('lolautruche'));
    }
}
