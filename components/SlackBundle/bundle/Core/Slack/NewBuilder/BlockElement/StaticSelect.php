<?php

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder\BlockElement;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlockElement;

class StaticSelect extends AbstractSlackBlockElement
{
    public function __construct(string $actionId, string $placeholder, array $options)
    {
        /**
         * The options should be the array like ['text' => 'value']
         */

        $this->options = [
            'type' => 'static_select',
            'action_id' => $actionId,
            'placeholder' => [
                'type' => 'plain_text',
                'text' => $placeholder
            ],
            'options' => array_map(
                static function ($key, $value) {
                    return [
                        'text' => [
                            'type' => 'plain_text',
                            'text' => $key
                        ],
                        'value' => $value
                    ];
                },
                array_keys($options), array_values($options)
            )
        ];
    }
}