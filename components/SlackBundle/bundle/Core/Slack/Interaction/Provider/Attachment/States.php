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

use Novactive\Bundle\eZSlackBundle\Core\Slack\SlackBlock\Actions;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Contracts\EventDispatcher\Event;

class States extends AttachmentProvider
{
    public function getAttachment(Event $event): ?array
    {
        if (count($this->actions) <= 0) {
            return null;
        }
        $actions = $this->buildActions($event);
        if (count($actions) <= 0) {
            return null;
        }

        $actionsBlock = new Actions();
        foreach ($actions as $action) {
            call_user_func_array([$actionsBlock, 'staticSelect'], $action);
        }

        return [
            new SlackDividerBlock(),
            (new SlackSectionBlock())->text($this->translator->trans('provider.states', [], 'slack'), false),
            $actionsBlock,
        ];
    }
}
