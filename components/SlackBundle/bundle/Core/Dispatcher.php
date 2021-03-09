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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Novactive\Bundle\eZSlackBundle\Repository\Trash as TrashRepository;
use eZ\Publish\API\Repository\Values\Content\Query as eZQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\TrashItem;

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
        //$message = $this->messageConverter->convert($event);
        if ('novactive_ezslack_callback_notification' === $this->requestStack->getCurrentRequest()->get('_route')) {
            return;
        }
        //$this->slackClient->sendNotification($message);

        // checking if the event is Trash and the Content is really in trash
        if ($event instanceof Events\Trash\TrashEvent &&
            !$this->trashRepository->checkIfContentIsInTrash($event->getLocation()->getContent())) {
            return;
        }

        $options = $this->messageConverter->convertToOptions($event);
        //dd($options->toArray());

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
