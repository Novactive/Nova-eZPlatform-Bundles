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
use eZ\Publish\API\Repository\Events\Trash\TrashEvent;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Symfony\Contracts\EventDispatcher\Event;

class Unhide extends ActionProvider
{
    public function getAction(Event $event): ?array
    {
        $content = $this->getContentForSignal($event);

        if (
            null === $content ||
            !$content->contentInfo->published ||
            null === $content->contentInfo->mainLocationId ||
            $event instanceof TrashEvent
        ) {
            return null;
        }

        if ($event instanceof Events\Content\HideContentEvent) {
            $actionId = $this->getAlias().'.content';
            $value = (string) $event->getContentInfo()->id;
        } elseif ($event instanceof Events\Location\HideLocationEvent) {
            $actionId = $this->getAlias().'.location';
            $value = (string) $event->getLocation()->id;
        } elseif (
            $event instanceof Events\Content\RevealContentEvent ||
            $event instanceof Events\Location\UnhideLocationEvent ||
            false === $content->contentInfo->isHidden
        ) {
            return null;
        } else {
            $actionId = $this->getAlias().'.content';
            $value = (string) $content->id;
        }

        return [
            'text' => $this->translator->trans('action.unhide', [], 'slack'),
            'action_id' => $actionId,
            'value' => $value,
            'style' => ActionProvider::PRIMARY_STYLE,
        ];
    }

    public function execute(InteractiveMessage $message, array $allActions = []): array
    {
        $messageAction = $message->getAction();
        $value = (int) $messageAction['value'];

        $response = [];
        try {
            if (str_ends_with($messageAction['action_id'], 'content')) {
                $content = $this->repository->getContentService()->loadContent($value);
                $this->repository->getContentService()->revealContent($content->contentInfo);
                $response['text'] = $this->translator->trans('action.content.unhid', [], 'slack');
                $event = new Events\Content\RevealContentEvent($content->contentInfo);
            } else {
                $location = $this->repository->getLocationService()->loadLocation($value);
                $this->repository->getLocationService()->unhideLocation($location);
                $response['text'] = $this->translator->trans('action.location.unhid', [], 'slack');
                $event = new Events\Location\UnhideLocationEvent($location, $location);
            }
        } catch (\Exception $e) {
            $response['text'] = $e->getMessage();

            return $response;
        }
        foreach ($allActions as $action) {
            if ($action instanceof Hide) {
                $block = $action->getAction($event);
                if (null !== $block) {
                    $block['text'] = [
                        'type' => 'plain_text',
                        'text' => $block['text'],
                    ];
                    $block['type'] = 'button';
                    $response['action'] = $block;
                }
                break;
            }
        }

        return $response;
    }
}
