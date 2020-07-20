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

namespace Novactive\Bundle\eZAccelerator\DependencyInjection\Compiler;

use Novactive\Bundle\eZAccelerator\Core\BusDispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class EventPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(BusDispatcher::class)) {
            return;
        }
        $busDisptacherListenerDef = $container->getDefinition(BusDispatcher::class);

        $busServiceKeys = array_keys($container->findTaggedServiceIds('messenger.bus'));

        foreach ($busServiceKeys as $id) {
            $busDisptacherListenerDef->addMethodCall('addBus', [$id, new Reference($id)]);
        }

        $eventListened = $container->getExtensionConfig('nova_ezaccelerator');

        foreach ($eventListened[0]['system'] as $config) {
            $events = array_keys($config['event_to_message']);
            foreach ($events as $event) {
                $busDisptacherListenerDef->addTag(
                    'kernel.event_listener',
                    [
                        'event'  => $event,
                        'method' => 'dispatchEvent',
                    ]
                );
            }
        }
    }
}
