<?php

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlock;

final class Action extends AbstractSlackBlock
{
    public function __construct()
    {
        $this->options['type'] = 'actions';
    }

    public function button(string $text, string $actionId, string $value, string $style = null): self
    {
        $element = [
            'type' => 'button',
            'text' => [
                'type' => 'plain_text',
                'text' => $text,
            ],
            'action_id' => $actionId,
            'value' => $value
        ];

        if ($style) {
            $element['style'] = $style;
        }

        $this->options['elements'][] = $element;

        return $this;
    }
}