<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\EventListener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuListener implements EventSubscriberInterface
{
    /** @var PermissionResolver */
    private $permissionResolver;

    /**
     * MenuListener constructor.
     */
    public function __construct(
        PermissionResolver $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', 0],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        if ($this->permissionResolver->hasAccess('solradmin', 'dashboard')) {
            $topMenuItem = $menu[MainMenuBuilder::ITEM_ADMIN]->addChild(
                'solr_admin',
                [
                    'label' => 'solr_admin',
                ]
            );

            $topMenuItem->addChild(
                'solr_admin.resources',
                [
                    'label' => 'solr_admin.resources',
                    'route' => 'solr_admin.dashboard',
                ]
            );
        }
    }
}
