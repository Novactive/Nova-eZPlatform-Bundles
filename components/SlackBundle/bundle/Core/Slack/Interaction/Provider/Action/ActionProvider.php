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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action;

use eZ\Publish\API\Repository\Events;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use Novactive\Bundle\eZSlackBundle\Core\Event\Searched;
use Novactive\Bundle\eZSlackBundle\Core\Event\Selected;
use Novactive\Bundle\eZSlackBundle\Core\Event\Shared;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\AliasTrait;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ActionProvider implements ActionProviderInterface
{
    use AliasTrait;

    /**
     * @var Repository
     */
    protected $repository;

    protected TranslatorInterface $translator;

    public const DEFAULT_STYLE = 'default';
    public const PRIMARY_STYLE = 'primary';
    public const DANGER_STYLE = 'danger';

    /**
     * @required
     *
     * @return ActionProvider
     */
    public function setRepository(Repository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @required
     *
     * @return ActionProvider
     */
    public function setTranslator(TranslatorInterface $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function supports($alias): bool
    {
        return 0 === strpos($alias, $this->getAlias());
    }

    protected function getContentForSignal(Event $event): ?Content
    {
        if ($event instanceof Shared || $event instanceof Selected || $event instanceof Searched) {
            return $this->repository->getContentService()->loadContent($event->getContentId());
        }
        if ($event instanceof Events\Content\PublishVersionEvent) {
            return $this->repository->getContentService()->loadContent($event->getContent()->id);
        }
        if (
            $event instanceof Events\Location\HideLocationEvent ||
            $event instanceof Events\Location\UnhideLocationEvent ||
            $event instanceof Events\Trash\TrashEvent ||
            $event instanceof Events\Trash\RecoverEvent
        ) {
            return $this->repository->getContentService()->loadContent($event->getLocation()->contentId);
        }
        if ($event instanceof Events\ObjectState\SetContentStateEvent) {
            return $this->repository->getContentService()->loadContent($event->getContentInfo()->id);
        }

        return null;
    }
}
