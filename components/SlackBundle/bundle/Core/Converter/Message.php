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
use Novactive\Bundle\eZSlackBundle\Core\Slack\SlackBlock\Section;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Message
{
    private InteractionProvider $provider;

    private TranslatorInterface $translator;

    public function __construct(InteractionProvider $provider, TranslatorInterface $translator)
    {
        $this->provider = $provider;
        $this->translator = $translator;
    }

    public function convert(Event $event, ?SlackOptions $slackOptions = null): SlackOptions
    {
        if (null === $slackOptions) {
            $slackOptions = new SlackOptions();
        }

        if (
            !isset($slackOptions->toArray()['blocks']) ||
            !in_array('title', array_column($slackOptions->toArray()['blocks'], 'block_id'), true)
        ) {
            $params = [];
            if ($event instanceof Events\Content\PublishVersionEvent) {
                $created = 'message.text.content.created';
                $updated = 'message.text.content.updated';
                $headerText = $event->getVersionInfo()->versionNo > 1 ? $updated : $created;
            }
            if ($event instanceof Events\Location\HideLocationEvent) {
                $headerText = 'message.text.location.hid';
                $params = ['%id%' => $event->getLocation()->id];
            }
            if ($event instanceof Events\Location\UnhideLocationEvent) {
                $headerText = 'message.text.location.unhid';
                $params = ['%id%' => $event->getLocation()->id];
            }
            if ($event instanceof Events\Content\HideContentEvent) {
                $headerText = 'message.text.content.hid';
            }
            if ($event instanceof Events\Content\RevealContentEvent) {
                $headerText = 'message.text.content.unhid';
            }
            if ($event instanceof Events\Trash\TrashEvent) {
                $headerText = 'message.text.content.trashed';
            }
            if ($event instanceof Events\Trash\RecoverEvent) {
                $headerText = 'message.text.content.recovered';
            }
            if ($event instanceof Events\ObjectState\SetContentStateEvent) {
                $headerText = 'message.text.content.state.updated';
            }
            if ($event instanceof Shared) {
                $headerText = 'message.text.content.shared';
            }

            // eZ Platform Enterprise
            if (is_a($event, 'EzSystems\EzPlatformFormBuilder\Event\FormSubmitEvent')) {
                $headerText = 'message.text.formsubmit';
            }

            if ($event instanceof Events\Notification\CreateNotificationEvent) {
                $headerText = 'message.text.notification';
            }

            if (isset($headerText)) {
                $slackOptions
                    ->block((new Section($this->translator->trans($headerText, $params, 'slack')))->blockId('title'))
                    ->block(new SlackDividerBlock());
            }
        }

        foreach ($this->provider->getAttachments($event) as $block) {
            $slackOptions->block($block);
        }

        return $slackOptions;
    }
}
