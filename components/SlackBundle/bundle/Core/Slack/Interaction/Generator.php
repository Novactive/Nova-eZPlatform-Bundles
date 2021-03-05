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

use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;

class Generator
{
    public function replaceBlockAction(array &$blocks, array $action, string $blockId, string $actionId): void
    {
        foreach ($blocks as $index => $block) {
            if ($block['block_id'] === $blockId) {
                $blockIndex = $index;
                $blockElementIndex = array_search(
                    $actionId,
                    array_column($block['elements'], 'action_id'),
                    true
                );
                $blocks[$blockIndex]['elements'][$blockElementIndex] = $action;

                break;
            }
        }
    }

    public function insertTextSection(array &$blocks, string $text, string $blockId): void
    {
        foreach ($blocks as $index => $block) {
            if ($block['block_id'] === $blockId) {
                $blockIndex = $index;
                $responseTextBlock = [
                    'type' => 'section',
                    'text' => [
                        'type' => 'plain_text',
                        'text' => $text
                    ]
                ];
                array_splice($blocks, $blockIndex, 0, [$responseTextBlock]);

                break;
            }
        }
    }

    //@todo: it's not obvious if it's neede, probably can be dropped with just an array used instead
    public function createMessage(array $payload): InteractiveMessage
    {
        $interactiveMessage = new InteractiveMessage();
        $interactiveMessage->setToken($payload['token']);
        $interactiveMessage->setActions($payload['actions']);
        $interactiveMessage->setBlocks($payload['message']['blocks']);
        $interactiveMessage->setResponseURL($payload['response_url']);

        return $interactiveMessage;
    }
}
