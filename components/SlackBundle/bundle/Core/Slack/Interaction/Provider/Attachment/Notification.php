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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Attachment;

use eZ\Publish\API\Repository\Events\Notification\CreateNotificationEvent;
use Symfony\Contracts\EventDispatcher\Event;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;

class Notification extends AttachmentProvider
{
    public function getAttachment(Event $event): ?Attachment
    {
        if (!$event instanceof CreateNotificationEvent) {
            return null;
        }
        $data = $event->getNotification()->data;

        $attachment = new Attachment();
        $attachment->setTitle($event->getNotification()->type);
        if (isset($data['receiver_id'])) {
            $this->attachmentDecorator->addAuthor($attachment, $data['receiver_id']);
            $attachment->setTitle($event->getNotification()->type.' -> '.$attachment->getAuthor()->getName());
        }
        $attachment->setText($data['message']);
        $attachment->setCallbackId($this->getAlias().'.'.time());
        if (isset($data['sender_id'])) {
            $this->attachmentDecorator->addAuthor($attachment, $data['sender_id']);
        }
        $this->attachmentDecorator->decorate($attachment, 'workflow');

        return $attachment;
    }
}
