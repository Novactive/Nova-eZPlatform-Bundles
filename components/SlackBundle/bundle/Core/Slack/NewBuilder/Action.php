<?php

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlock;

final class Action extends AbstractSlackBlock
{
    public function __construct()
    {
        $this->options['type'] = 'actions';
    }

    public function button(
        string $text,
        string $actionId,
        string $value,
        ?string $style = null,
        ?array $confirm = null
    ): self {
        $element = [
            'type' => 'button',
            'text' => [
                'type' => 'plain_text',
                'text' => $text,
            ],
            'action_id' => $actionId,
            'value' => $value
        ];

        if (null !== $style) {
            $element['style'] = $style;
        }

        if (null !== $confirm) {
            $element['confirm'] = [
                'title' => [
                    'type' => 'plain_text',
                    'text' => $confirm['title']
                ],
                'text' => [
                    'type' => 'plain_text',
                    'text' => $confirm['text']
                ],
                'confirm' => [
                    'type' => 'plain_text',
                    'text' => $confirm['confirm']
                ],
                'deny' => [
                    'type' => 'plain_text',
                    'text' => $confirm['deny']
                ]
            ];
        }

        $this->options['elements'][] = $element;

        return $this;
    }

    public function staticSelect(string $placeholder, string $actionId, array $options): self
    {
        $element = [
            'type' => 'static_select',
            'placeholder' => [
                'type' => 'plain_text',
                'text' => $placeholder,
            ],
            'action_id' => $actionId,
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
                array_keys($options),
                array_values($options)
            )
        ];

        $this->options['elements'][] = $element;

        return $this;
    }
}