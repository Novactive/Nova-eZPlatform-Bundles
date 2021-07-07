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

use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Symfony\Contracts\EventDispatcher\Event;

class PublicationChainChangeState extends ActionProvider
{
    public function getAction(Event $event): ?array
    {
        $content = $this->getContentForSignal($event);
        if (null === $content) {
            return null;
        }

        try {
            $chainGroup = null;
            $objectStateService = $this->repository->getObjectStateService();
            $allGroups = $objectStateService->loadObjectStateGroups();
            foreach ($allGroups as $group) {
                if ('publication_chain' === $group->identifier) {
                    $chainGroup = $group;
                    break;
                }
            }
            if (null === $chainGroup) {
                return null;
            }
            $states = $objectStateService->loadObjectStates($chainGroup);

            $select = [
                'placeholder' => $this->translator->trans('action.publication_chain.change_state', [], 'slack'),
                'action_id' => $this->getAlias(),
            ];

            foreach ($states as $state) {
                $select['options'][$state->getNames()[$state->mainLanguageCode]] = "{$content->id}:{$state->id}";
            }

            return $select;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function execute(InteractiveMessage $messageAction, array $allActions = []): array
    {
        $action = $messageAction->getAction();
        [$contentId, $value] = explode(':', $action['selected_option']['value']);

        $response = [];

        try {
            $content = $this->repository->getContentService()->loadContent((int) $contentId);
            $state = $this->repository->getObjectStateService()->loadObjectState((int) $value);
            $contentState = $this->repository->getObjectStateService()->getContentState(
                $content->contentInfo,
                $state->getObjectStateGroup()
            );

            if ($state->id !== $contentState->id) {
                $this->repository->getObjectStateService()->setContentState(
                    $content->contentInfo,
                    $state->getObjectStateGroup(),
                    $state
                );
                $response['text'] = $this->translator->trans('action.state.changed', [], 'slack');
            }
        } catch (\Exception $e) {
            $response['text'] = $e->getMessage();
        }

        return $response;
    }
}
