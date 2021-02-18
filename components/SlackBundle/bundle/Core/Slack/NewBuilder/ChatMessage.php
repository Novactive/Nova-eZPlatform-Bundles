<?php

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\NewBuilder;

use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\MessageOptionsInterface;

final class ChatMessage implements MessageInterface
{
    private $transport;
    private $subject;
    private $options;
    private $notification;

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getRecipientId(): ?string
    {
        return $this->options ? $this->options->getRecipientId() : null;
    }

    public function getOptions(): ?MessageOptionsInterface
    {
        return $this->options;
    }

    public function getTransport(): ?string
    {
        return $this->transport;
    }
}