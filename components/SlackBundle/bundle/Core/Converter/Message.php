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

namespace Novactive\Bundle\eZSlackBundle\Core\Converter;

use eZ\Publish\API\Repository\Events;
use Novactive\Bundle\eZSlackBundle\Core\Event\Shared;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider as InteractionProvider;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Message as MessageModel;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Message
{
    private InteractionProvider $provider;

    public function __construct(InteractionProvider $provider)
    {
        $this->provider = $provider;
    }

    public function convert(Event $event, ?MessageModel $message = null): MessageModel
    {
        if (null === $message) {
            $message = new MessageModel();
        }

        if (null === $message->getText()) {
            if ($event instanceof Events\Content\PublishVersionEvent) {
                $created = '_t:message.text.content.created';
                $updated = '_t:message.text.content.updated';
                $message->setText(
                    $event->getVersionInfo()->versionNo > 1 ? $updated : $created
                );
            }
            if ($event instanceof Events\Location\HideLocationEvent) {
                $message->setText('_t:message.text.content.hid');
            }
            if ($event instanceof Events\Location\UnhideLocationEvent) {
                $message->setText('_t:message.text.content.unhid');
            }
            if ($event instanceof Events\Trash\TrashEvent) {
                $message->setText('_t:message.text.content.trashed');
            }
            if ($event instanceof Events\Trash\RecoverEvent) {
                $message->setText('_t:message.text.content.recovered');
            }
            if ($event instanceof Events\ObjectState\SetContentStateEvent) {
                $message->setText('_t:message.text.content.state.updated');
            }
            if ($event instanceof Shared) {
                $message->setText('_t:message.text.content.shared');
            }

            // eZ Platform Enterprise
            if (is_a($event, 'EzSystems\EzPlatformFormBuilder\Event\FormSubmitEvent')) {
                $message->setText('_t:message.text.formsubmit');
            }

            if ($event instanceof Events\Notification\CreateNotificationEvent) {
                $message->setText('_t:message.text.notification');
            }
        }
        $attachments = $this->provider->getAttachments($event);
        $message->setAttachments($attachments);

        return $message;
    }
}
