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

use Exception;
use eZ\Publish\API\Repository\Events;
use eZ\Publish\API\Repository\Events\Trash\TrashEvent;
use eZ\Publish\API\Repository\Values\Content\Query as eZQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Symfony\Contracts\EventDispatcher\Event;

class Recover extends ActionProvider
{
    public function getAction(Event $event): ?array
    {
        if (!$event instanceof TrashEvent) {
            return null;
        }

        return [
            'text' => $this->translator->trans('action.recover', [], 'slack'),
            'action_id' => $this->getAlias(),
            'value' => (string) $event->getLocation()->contentId,
            'style' => ActionProvider::PRIMARY_STYLE,
            'confirm' => [
                'title' => $this->translator->trans('action.recover', [], 'slack'),
                'text' => $this->translator->trans('action.generic.confirmation', [], 'slack'),
                'confirm' => $this->translator->trans('action.confirmation.confirm', [], 'slack'),
                'deny' => $this->translator->trans('action.confirmation.deny', [], 'slack'),
            ],
        ];
    }

    public function execute(InteractiveMessage $message, array $allActions = []): array
    {
        $action = $message->getAction();
        $value = (int) $action['value'];

        $response = [];

        try {
            $query = new eZQuery();
            $query->filter = new Criterion\ContentTypeId(
                $this->repository->getContentService()->loadContent($value)->contentInfo->contentTypeId
            );
            $results = $this->repository->getTrashService()->findTrashItems($query);
            foreach ($results->items as $item) {
                if ($item->contentInfo->id === $value) {
                    $location = $this->repository->getTrashService()->recover($item);
                    $event = new Events\Trash\RecoverEvent($location, $item);
                }
            }
            $response['text'] = $this->translator->trans('action.items.recovered', [], 'slack');
        } catch (Exception $e) {
            $response['text'] = $e->getMessage();

            return $response;
        }

        if (isset($event)) {
            foreach ($allActions as $action) {
                if ($action instanceof Trash) {
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
