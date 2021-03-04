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

use Novactive\Bundle\eZSlackBundle\Core\Slack\Action;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Button;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Symfony\Contracts\EventDispatcher\Event;
use eZ\Publish\API\Repository\Events;

class Hide extends ActionProvider
{
    public function getAction(Event $event, int $index): ?Action
    {
        $content = $this->getContentForSignal($event);
        if (null === $content || !$content->contentInfo->published || null === $content->contentInfo->mainLocationId) {
            return null;
        }

        $location = $this->repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);
        if ($location->hidden) {
            return null;
        }
        $button = new Button($this->getAlias(), '_t:action.hide', (string) $content->id);
        $button->setStyle(Button::DANGER_STYLE);

        return $button;
    }

    public function getNewAction(Event $event): ?array
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
            'style' => ActionProvider::DANGER_STYLE
        ];
    }

    //    public function execute(InteractiveMessage $message): Attachment
    //    {
    //        $action = $message->getAction();
    //        $value = (int) $action['value'];
    //
    //        $attachment = new Attachment();
    //        $attachment->setTitle('_t:action.hide');
    //        try {
    //            $content = $this->repository->getContentService()->loadContent($value);
    //            $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
    //            foreach ($locations as $location) {
    //                $this->repository->getLocationService()->hideLocation($location);
    //            }
    //            $attachment->setColor('good');
    //            $attachment->setText('_t:action.locations.hid');
    //        } catch (\Exception $e) {
    //            $attachment->setColor('danger');
    //            $attachment->setText($e->getMessage());
    //        }
    //
    //        return $attachment;
    //    }

    public function execute(InteractiveMessage $message): array
    {
        $action = $message->getAction();
        $value = (int) $action['value'];

        $response = [];
        try {
            $content = $this->repository->getContentService()->loadContent($value);
            $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
            foreach ($locations as $location) {
                $this->repository->getLocationService()->hideLocation($location);
            }
            $response['text'] = $this->translator->trans('action.locations.hid', [], 'slack');
        } catch (\Exception $e) {
            $response['text'] = $e->getMessage();
        }

        return $response;
    }
}
