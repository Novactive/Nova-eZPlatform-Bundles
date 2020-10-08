<?php

/**
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 */

namespace Novactive\EzRssFeedBundle\PlatformAdminUI\EventListener;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Repository;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MainMenuBuilderListener implements EventSubscriberInterface
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Repository $repository)
    {
        $this->repository = $repository;
    }

    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::MAIN_MENU => 'onMainMenuBuild'];
    }

    public function onMainMenuBuild(ConfigureMenuEvent $event): void
    {
        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->getRepository()->getPermissionResolver();

        if ($permissionResolver->hasAccess('rss', 'edit')) {
            $this->addSubMenu($event->getMenu());
        }
    }

    public function getRepository(): Repository
    {
        return $this->repository;
    }

    /**
     * Adds the RSS submenu to eZ Platform admin interface.
     */
    private function addSubMenu(ItemInterface $menu): void
    {
        $menu
            ->addChild('rss', ['route' => 'platform_admin_ui_rss_feeds_list'])
            ->setLabel('ez_rss_feed.menu.main_menu.header');
    }
}
