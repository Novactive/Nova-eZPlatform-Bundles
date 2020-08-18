<?php

/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Listener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;

/**
 * Class TopMenu.
 */
class TopMenu
{
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $menu->addChild(
            'eznovamailing',
            [
                'route' => 'novaezmailing_dashboard_index',
                'label' => 'Nova eZ Mailing',
            ]
        );
    }
}
