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

use Novactive\Bundle\eZSlackBundle\Core\Event\Searched;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder\Action;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Contracts\EventDispatcher\Event;

class BasicActions extends AttachmentProvider
{
    public function getAttachment(Event $event): ?Attachment
    {
        if ($event instanceof Searched || count($this->actions) <= 0) {
            return null;
        }
        $attachment = new Attachment();
        $attachment->setColor('#0000ff');
        $attachment->setText('_t:provider.basic-buttons');
        $actions = $this->buildActions($event);
        if (count($actions) <= 0) {
            return null;
        }
        $attachment->setActions($actions);
        $attachment->setCallbackId($this->getAlias().'.'.time());

        $this->attachmentDecorator->decorate($attachment);

        return $attachment;
    }

    public function getAttachmentBlocks(Event $event): array
    {
        if ($event instanceof Searched || count($this->actions) <= 0) {
            return [];
        }
        $actions = $this->buildActions($event);
        if (count($actions) <= 0) {
            return [];
        }

        $actionsBlock = new Action();
        foreach ($actions as $action) {
            call_user_func_array([$actionsBlock, 'button'], $action);
        }

        return [
            (new SlackSectionBlock())->text($this->translator->trans('provider.basic-buttons', [], 'slack'), false),
            $actionsBlock
        ];
    }
}
