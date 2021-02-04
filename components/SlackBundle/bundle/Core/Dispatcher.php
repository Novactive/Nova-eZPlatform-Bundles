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

use Novactive\Bundle\eZSlackBundle\Core\Client\Slack;
use Novactive\Bundle\eZSlackBundle\Core\Converter\Message as MessageConverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use eZ\Publish\API\Repository\Events;
use Symfony\Contracts\EventDispatcher\Event;
use Novactive\Bundle\eZSlackBundle\Core\Event\Shared;

class Dispatcher implements EventSubscriberInterface
{
    private Slack $slackClient;

    private MessageConverter $messageConverter;

    public function __construct(Slack $slackClient, MessageConverter $messageConverter)
    {
        $this->slackClient = $slackClient;
        $this->messageConverter = $messageConverter;
    }

    public function receive(Event $event): void
    {
        $message = $this->messageConverter->convert($event);
        $this->slackClient->sendNotification($message);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events\Content\PublishVersionEvent::class => 'receive',
            Events\Location\HideLocationEvent::class => 'receive',
            Events\Location\UnhideLocationEvent::class => 'receive',
            Events\Trash\TrashEvent::class => 'receive',
            Events\Trash\RecoverEvent::class => 'receive',
            Events\ObjectState\SetContentStateEvent::class => 'receive',
            Shared::class => 'receive',
            'EzSystems\EzPlatformFormBuilder\Event\FormSubmitEvent' => 'receive',
            Events\Notification\CreateNotificationEvent::class => 'receive',
        ];
    }
}
