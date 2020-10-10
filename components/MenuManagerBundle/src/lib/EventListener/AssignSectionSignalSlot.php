<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    florian
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzMenuManager\EventListener;

use eZ\Publish\API\Repository\Events\Section\AssignSectionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssignSectionSignalSlot implements EventSubscriberInterface
{
    use CachePurgerTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            AssignSectionEvent::class => 'onAssignSection',
        ];
    }

    public function onAssignSection(AssignSectionEvent $event): void
    {
        $this->purgeContentMenuItemCache($event->getContentInfo()->id);
    }
}
