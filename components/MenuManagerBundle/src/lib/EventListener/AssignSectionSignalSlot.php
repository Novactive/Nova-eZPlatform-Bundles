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

use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\Slot;

class AssignSectionSignalSlot extends Slot
{
    use CachePurgerTrait;

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function receive(Signal $signal): void
    {
        if (!$signal instanceof Signal\SectionService\AssignSectionSignal) {
            return;
        }
        $this->purgeContentMenuItemCache($signal->contentId);
    }
}
