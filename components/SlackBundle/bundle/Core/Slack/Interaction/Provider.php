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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction;

use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Attachment\AttachmentProviderInterface;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use RuntimeException;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class Provider.
 */
class Provider
{
    /**
     * @var AttachmentProviderInterface[]
     */
    private $attachmentProviders;

    public function addAttachmentProvider(AttachmentProviderInterface $provider, string $alias): void
    {
        $provider->setAlias($alias);
        $this->attachmentProviders[$alias] = $provider;
    }

    public function execute(InteractiveMessage $message): Attachment
    {
        $action = $message->getAction();
        foreach ($this->attachmentProviders as $provider) {
            if ($provider->supports($action->getName())) {
                return $provider->execute($message);
            }
        }
        throw new RuntimeException("No Attachment Provider supports '{$action->getName()}'.");
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments(Event $event): array
    {
        $attachments = [];
        foreach ($this->attachmentProviders as $provider) {
            $attachment = $provider->getAttachment($event);
            if (null !== $attachment) {
                $attachments[] = $attachment;
            }
        }

        return $attachments;
    }
}
