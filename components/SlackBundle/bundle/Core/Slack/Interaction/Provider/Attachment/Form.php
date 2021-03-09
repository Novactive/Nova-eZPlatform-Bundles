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

use Symfony\Contracts\EventDispatcher\Event;

class Form extends AttachmentProvider
{
    public function getAttachment(Event $event): ?array
    {
        if (false === is_a($event, 'EzSystems\EzPlatformFormBuilder\Event\FormSubmitEvent')) {
            return null;
        }

        return null;
    }
}
