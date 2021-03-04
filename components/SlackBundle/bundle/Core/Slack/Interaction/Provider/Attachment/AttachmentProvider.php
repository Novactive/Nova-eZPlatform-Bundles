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

use Novactive\Bundle\eZSlackBundle\Core\Decorator\Attachment as AttachmentDecorator;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Action;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action\ActionProvider;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action\ActionProviderInterface;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\AliasTrait;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use RuntimeException;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AttachmentProvider implements AttachmentProviderInterface
{
    use AliasTrait;

    /**
     * @var ActionProviderInterface[]
     */
    protected array $actions;

    /**
     * @var AttachmentDecorator
     */
    protected $attachmentDecorator;

    protected TranslatorInterface $translator;

    /**
     * @required
     *
     * @return AttachmentProvider
     */
    public function setAttachmentDecorator(AttachmentDecorator $attachmentDecorator): self
    {
        $this->attachmentDecorator = $attachmentDecorator;

        return $this;
    }

    /**
     * @required
     *
     * @return AttachmentProvider
     */
    public function setTranslator(TranslatorInterface $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @return BasicActions
     */
    public function addAction(ActionProviderInterface $action, string $alias): self
    {
        $action->setAlias($alias);
        $this->actions[$alias] = $action;

        return $this;
    }

    public function buildActions(Event $event): array
    {
        $actions = [];
        foreach ($this->actions as $index => $actionProvider) {
            /* @var ActionProvider $actionProvider */
            $action = $actionProvider->getNewAction($event, (int) $index);
            if (null !== $action) {
                $actions[] = $action;
            }
        }

        return $actions;
    }

    public function supports($alias): bool
    {
        return 0 === strpos($alias, $this->getAlias());
    }

    public function execute(InteractiveMessage $message): array
    {
        $action = $message->getAction();
        foreach ($this->actions as $provider) {
            if ($provider->supports($action['action_id'])) {
                return $provider->execute($message);
            }
        }

        throw new RuntimeException("No Action Provider supports '{$action['action_id']}'.");
    }

    public function executeBlocks(InteractiveMessage $message): array
    {
        $action = $message->getAction();
        foreach ($this->actions as $provider) {
            if ($provider->supports($action['action_id'])) {
                return $provider->execute($message);
            }
        }

        throw new RuntimeException("No Action Provider supports '{$action['action_id']}'.");
    }
}
