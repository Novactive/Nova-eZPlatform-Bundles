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

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;

class AdminTopMenu
{
    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        if (isset($menu[MainMenuBuilder::ITEM_ADMIN]) && null !== $menu[MainMenuBuilder::ITEM_ADMIN]) {
            $menu[MainMenuBuilder::ITEM_ADMIN]->addChild(
                'manage_maintenane',
                [
                    'label' => 'Maintenance',
                    'route' => 'novamaintenance_manage',
                ]
            );
        }
    }
}
