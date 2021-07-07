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
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Symfony\Contracts\EventDispatcher\Event;

class Hide extends ActionProvider
{
    public function getAction(Event $event): ?array
    {
        $content = $this->getContentForSignal($event);
        if (null === $content || !$content->contentInfo->published || null === $content->contentInfo->mainLocationId) {
            return null;
        }

        if ($event instanceof Events\Content\RevealContentEvent) {
            $actionId = $this->getAlias().'.content';
            $value = (string) $event->getContentInfo()->id;
        } elseif ($event instanceof Events\Location\UnhideLocationEvent) {
            $actionId = $this->getAlias().'.location';
            $value = (string) $event->getLocation()->id;
        } elseif (
            $event instanceof Events\Content\HideContentEvent ||
            $event instanceof Events\Location\HideLocationEvent ||
            $content->contentInfo->isHidden
        ) {
            return null;
        } else {
            $actionId = $this->getAlias().'.content';
            $value = (string) $content->id;
        }

        return [
            'text' => $this->translator->trans('action.hide', [], 'slack'),
            'action_id' => $actionId,
            'value' => $value,
            'style' => ActionProvider::DANGER_STYLE,
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
                $this->repository->getContentService()->hideContent($content->contentInfo);
                $response['text'] = $this->translator->trans('action.content.hid', [], 'slack');
                $event = new Events\Content\HideContentEvent($content->contentInfo);
            } else {
                $location = $this->repository->getLocationService()->loadLocation($value);
                $this->repository->getLocationService()->hideLocation($location);
                $response['text'] = $this->translator->trans('action.location.hid', [], 'slack');
                $event = new Events\Location\HideLocationEvent($location, $location);
            }
        } catch (\Exception $e) {
            $response['text'] = $e->getMessage();

            return $response;
        }
        foreach ($allActions as $action) {
            if ($action instanceof Unhide) {
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
