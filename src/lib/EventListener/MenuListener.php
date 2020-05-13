<?php

/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

namespace Novactive\EzSolrSearchExtra\EventListener;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use Knp\Menu\ItemInterface;
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

    public static function getSubscribedEvents()
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', 0],
        ];
    }

    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        if ($this->permissionResolver->hasAccess('solradmin', 'dashboard')) {
            /** @var ItemInterface $topMenuItem */
            $topMenuItem = $menu->addChild(
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
