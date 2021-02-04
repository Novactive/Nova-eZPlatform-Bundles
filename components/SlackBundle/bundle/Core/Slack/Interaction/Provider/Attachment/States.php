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

use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use eZ\Publish\API\Repository\Events\ObjectState\SetContentStateEvent;
use Symfony\Contracts\EventDispatcher\Event;

class States extends AttachmentProvider
{
    public function getAttachment(Event $event): ?Attachment
    {
        if (!$event instanceof SetContentStateEvent) {
            return null;
        }
        $attachment = new Attachment();
        $attachment->setText('_t:provider.states');
        $actions = $this->buildActions($event);
        if (count($actions) <= 0) {
            return null;
        }
        $attachment->setActions($actions);
        $attachment->setCallbackId($this->getAlias().'.'.time());
        $this->attachmentDecorator->decorate($attachment, 'states');

        return $attachment;
    }
}
