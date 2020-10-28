<?php

/**
 * NovaeZMaintenanceBundle.
 *
 * @package   Novactive\NovaeZMaintenanceBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZMaintenanceBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\NovaeZMaintenanceBundle\Listener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;

class AdminTopMenu
{
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        if (isset($menu[MainMenuBuilder::ITEM_ADMIN]) && null !== $menu[MainMenuBuilder::ITEM_ADMIN]) {
            $menu[MainMenuBuilder::ITEM_ADMIN]->addChild(
                'manage_maintenane',
                [
                    'label' => 'maintenance.admin.title',
                    'route' => 'novamaintenance_index',
                    'extras' => [
                        'translation_domain' => FileHelper::CONFIG_NAMESPACE,
                    ],
                ]
            );
        }
    }
}
