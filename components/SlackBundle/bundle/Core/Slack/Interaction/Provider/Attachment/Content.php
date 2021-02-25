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

use eZ\Publish\API\Repository\Events;
use Novactive\Bundle\eZSlackBundle\Core\Converter\Attachment as AttachmentConverter;
use Novactive\Bundle\eZSlackBundle\Core\Event\Searched;
use Novactive\Bundle\eZSlackBundle\Core\Event\Selected;
use Novactive\Bundle\eZSlackBundle\Core\Event\Shared;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackBlockInterface;
use Symfony\Contracts\EventDispatcher\Event;

class Content extends AttachmentProvider
{
    private AttachmentConverter $converter;

    public function __construct(AttachmentConverter $converter)
    {
        $this->converter = $converter;
    }

    public function getAttachment(Event $event): ?Attachment
    {
        $contentId = 0;
        if ($event instanceof Shared || $event instanceof Selected || $event instanceof Searched) {
            $contentId = $event->getContentId();
        } elseif ($event instanceof Events\Content\PublishVersionEvent) {
            $contentId = $event->getContent()->id;
        } elseif (
            $event instanceof Events\Location\HideLocationEvent ||
            $event instanceof Events\Location\UnhideLocationEvent ||
            $event instanceof Events\Trash\TrashEvent ||
            $event instanceof Events\Trash\RecoverEvent
        ) {
            $contentId = $event->getLocation()->contentId;
        } elseif ($event instanceof Events\ObjectState\SetContentStateEvent) {
            $contentId = $event->getContentInfo()->id;
        }

        dump($contentId);
        dump($this->getAlias());

        if ($contentId > 0) {
            if ('novaezslack.provider.main' === $this->getAlias()) {
                return $this->converter->getMain($contentId);
            }

            if (!$event instanceof Searched && 'novaezslack.provider.details' === $this->getAlias()) {
                return $this->converter->getDetails($contentId);
            }
            if (!$event instanceof Searched && 'novaezslack.provider.preview' === $this->getAlias()) {
                return $this->converter->getPreview($contentId);
            }
        }

        return null;
    }

    public function getAttachmentBlocks(Event $event): array
    {
        $contentId = 0;
        if ($event instanceof Shared || $event instanceof Selected || $event instanceof Searched) {
            $contentId = $event->getContentId();
        } elseif ($event instanceof Events\Content\PublishVersionEvent) {
            $contentId = $event->getContent()->id;
        } elseif (
            $event instanceof Events\Location\HideLocationEvent ||
            $event instanceof Events\Location\UnhideLocationEvent ||
            $event instanceof Events\Trash\TrashEvent ||
            $event instanceof Events\Trash\RecoverEvent
        ) {
            $contentId = $event->getLocation()->contentId;
        } elseif ($event instanceof Events\ObjectState\SetContentStateEvent) {
            $contentId = $event->getContentInfo()->id;
        }

        if ($contentId > 0) {
            if ('novaezslack.provider.main' === $this->getAlias()) {
                return $this->converter->getMainBlocks($contentId);
            }

            if (!$event instanceof Searched && 'novaezslack.provider.details' === $this->getAlias()) {
                return $this->converter->getDetailsBlock($contentId);
            }
            if (!$event instanceof Searched && 'novaezslack.provider.preview' === $this->getAlias()) {
                return $this->converter->getPreviewBlock($contentId);
            }
        }

        return [];
    }
}
