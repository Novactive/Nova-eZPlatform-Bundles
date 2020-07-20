<?php

/**
 * Nova eZ Accelerator.
 *
 * @package   Novactive\Bundle\eZAccelerator
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @author    Sébastien Morel (Plopix) <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZAccelerator/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAccelerator\Core;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use RuntimeException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class BusDispatcher
{
    /**
     * @var MessageBusInterface[]
     */
    private $buses;

    /**
     * @var MessageBusInterface
     */
    private $defaultBus;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver, MessageBusInterface $defaultBus)
    {
        $this->defaultBus     = $defaultBus;
        $this->configResolver = $configResolver;
    }

    public function addBus(string $id, MessageBusInterface $bus): void
    {
        $this->buses[$id] = $bus;
    }

    public function dispatch($message): void
    {
        $bus = $this->buses[$this->configResolver->getParameter('default_bus', 'nova_ezaccelerator')] ?? null;
        if (null === $bus) {
            $bus = $this->defaultBus;
        }
        $bus->dispatch($message);
    }

    public function dispatchEvent(Event $event): void
    {
        $configMap = $this->configResolver->getParameter('event_to_message', 'nova_ezaccelerator');

        $config = $configMap[\get_class($event)] ?? null;

        if (null === $config) {
            return;
        }

        $bus = $this->defaultBus;

        $defaultBusName = $this->configResolver->getParameter('default_bus', 'nova_ezaccelerator');
        if (null !== $defaultBusName) {
            $bus = $this->buses[$this->configResolver->getParameter('default_bus', 'nova_ezaccelerator')] ?? null;
            if (null === $bus) {
                $exceptionMessage = '[eZ Accelerator] Default Bus %s does not exist.';
                throw new RuntimeException(sprintf($exceptionMessage, $defaultBusName));
            }
        }

        if (isset($config['bus'])) {
            $bus = $this->buses[$config['bus']] ?? null;
            if (null === $bus) {
                $exceptionMessage = '[eZ Accelerator] Bus %s for message type %s does not exist.';
                throw new RuntimeException(sprintf($exceptionMessage, $config['bus'], $config['message']));
            }
        }

        $messageClass = $config['message'];
        $message      = new $messageClass($event);
        $bus->dispatch($message);

        if (true === $config['stop_propagation']) {
            $event->stopPropagation();
        }
    }
}
