<?php

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlock;

final class Section extends AbstractSlackBlock
{
    public function __construct(string $text, string $type = 'plain_text')
    {
        $this->options['type'] = 'section';
        $this->options['text'] = [
            'type' => $type,
            'text' => $text
        ];
    }

    public function blockId(string $blockId): self
    {
        $this->options['block_id'] = $blockId;

        return $this;
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

        $this->options['accessory'] = $element;

        return $this;
    }
}