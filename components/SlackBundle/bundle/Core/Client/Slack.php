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

namespace Novactive\Bundle\eZSlackBundle\Core\Client;

use Exception;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

class Slack
{
    private ChatterInterface $chatter;

    private array $transports;

    public function __construct(ConfigResolverInterface $configResolver, ChatterInterface $chatter)
    {
        $this->transports = $configResolver->getParameter('notifications', 'nova_ezslack')['transports'];
        $this->chatter = $chatter;
    }

    public function sendMessage(SlackOptions $options, string $subject = 'Interactive'): void
    {
        foreach ($this->transports as $transport) {
            $chatMessage = new ChatMessage($subject);
            $chatMessage->transport($transport);
            $chatMessage->options($options);
            try {
                $this->chatter->send($chatMessage);
            } catch (Exception $e) {
                // it is common slack would timeout, then we don't care
                continue;
            }
        }
    }
}
