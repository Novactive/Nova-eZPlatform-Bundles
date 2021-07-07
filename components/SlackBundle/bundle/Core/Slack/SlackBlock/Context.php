<?php

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\SlackBlock;

use Symfony\Component\Notifier\Bridge\Slack\Block\AbstractSlackBlock;

final class Context extends AbstractSlackBlock
{
    public function __construct()
    {
        $this->options['type'] = 'context';
    }

    public function image(string $imageUrl, string $altText): self
    {
        $this->options['elements'][] = [
            'type' => 'image',
            'image_url' => $imageUrl,
            'alt_text' => $altText,
        ];

        return $this;
    }

    public function text(string $text, string $type = 'mrkdwn'): self
    {
        $this->options['elements'][] = [
            'type' => $type,
            'text' => $text,
        ];

        return $this;
    }
}
