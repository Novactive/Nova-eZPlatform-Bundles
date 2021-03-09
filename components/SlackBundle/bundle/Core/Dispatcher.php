<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Core;

use eZ\Publish\API\Repository\Events;
use Novactive\Bundle\eZSlackBundle\Core\Client\Slack;
use Novactive\Bundle\eZSlackBundle\Core\Converter\Message as MessageConverter;
use Novactive\Bundle\eZSlackBundle\Core\Event\Shared;
use Novactive\Bundle\eZSlackBundle\Repository\Trash as TrashRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\Event;

class Dispatcher implements EventSubscriberInterface
{
    private Slack $slackClient;

    private MessageConverter $messageConverter;

    private RequestStack $requestStack;

    private TrashRepository $trashRepository;

    public function __construct(
        Slack $slackClient,
        MessageConverter $messageConverter,
        RequestStack $requestStack,
        TrashRepository $trashRepository
    ) {
        $this->slackClient = $slackClient;
        $this->messageConverter = $messageConverter;
        $this->requestStack = $requestStack;
        $this->trashRepository = $trashRepository;
    }

    public function receive(Event $event): void
    {
        if (
            null !== $this->requestStack->getCurrentRequest() &&
            'novactive_ezslack_callback_notification' === $this->requestStack->getCurrentRequest()->get('_route')
        ) {
            return;
        }

        // checking if the event is Trash and the Content is really in trash
        if (
            $event instanceof Events\Trash\TrashEvent &&
            !$this->trashRepository->checkIfContentIsInTrash($event->getLocation()->getContent())
        ) {
            return;
        }

        $options = $this->messageConverter->convert($event);
        $this->slackClient->sendMessage($options);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events\Content\PublishVersionEvent::class => 'receive',
            Events\Location\HideLocationEvent::class => 'receive',
            Events\Location\UnhideLocationEvent::class => 'receive',
            Events\Content\HideContentEvent::class => 'receive',
            Events\Content\RevealContentEvent::class => 'receive',
            Events\Trash\TrashEvent::class => 'receive',
            Events\Trash\RecoverEvent::class => 'receive',
            Events\ObjectState\SetContentStateEvent::class => 'receive',
            Shared::class => 'receive',
            'EzSystems\EzPlatformFormBuilder\Event\FormSubmitEvent' => 'receive',
            Events\Notification\CreateNotificationEvent::class => 'receive',
        ];
    }
}
