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

class Trash extends ActionProvider
{
    public function getAction(Event $event): ?array
    {
        $content = $this->getContentForSignal($event);
        if (null === $content || !$content->contentInfo->published || $event instanceof TrashEvent) {
            return null;
        }

        return [
            'text' => $this->translator->trans('action.trash', [], 'slack'),
            'action_id' => $this->getAlias(),
            'value' => (string) $content->id,
            'style' => ActionProvider::DANGER_STYLE,
            'confirm' => [
                'title' => $this->translator->trans('action.trash', [], 'slack'),
                'text' => $this->translator->trans('action.generic.confirmation', [], 'slack'),
                'confirm' => $this->translator->trans('action.confirmation.confirm', [], 'slack'),
                'deny' => $this->translator->trans('action.confirmation.deny', [], 'slack'),
            ],
        ];
    }

    public function execute(InteractiveMessage $message, array $allActions = []): array
    {
        $messageAction = $message->getAction();
        $value = (int) $messageAction['value'];

        $response = [];
        try {
            $content = $this->repository->getContentService()->loadContent($value);
            if ($content->contentInfo->published) {
                $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
                foreach ($locations as $location) {
                    $trashItem = $this->repository->getTrashService()->trash($location);
                    if ($location->id === $content->contentInfo->mainLocationId) {
                        $event = new Events\Trash\TrashEvent($trashItem, $location);
                    }
                }
                $response['text'] = $this->translator->trans('action.locations.trashed', [], 'slack');
            } else {
                $response['text'] = var_export($content->contentInfo, true);
            }
        } catch (\Exception $e) {
            $response['text'] = $e->getMessage();

            return $response;
        }
        if (isset($event)) {
            foreach ($allActions as $action) {
                if ($action instanceof Recover) {
                    $block = $action->getAction($event);
                    if (null !== $block) {
                        $block['text'] = [
                            'type' => 'plain_text',
                            'text' => $block['text'],
                        ];
                        $block['type'] = 'button';
                        $block['confirm'] = [
                            'title' => [
                                'type' => 'plain_text',
                                'text' => $block['confirm']['title'],
                            ],
                            'text' => [
                                'type' => 'plain_text',
                                'text' => $block['confirm']['text'],
                            ],
                            'confirm' => [
                                'type' => 'plain_text',
                                'text' => $block['confirm']['confirm'],
                            ],
                            'deny' => [
                                'type' => 'plain_text',
                                'text' => $block['confirm']['deny'],
                            ],
                        ];
                        $response['action'] = $block;
                    }
                    break;
                }
            }
        }

        return $response;
    }
}
