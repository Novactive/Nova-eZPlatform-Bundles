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
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    private $repository;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Repository $repository)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->repository           = $repository;
    }

    public static function getSubscribedEvents()
    {
        return [ConfigureMenuEvent::MAIN_MENU => 'onMainMenuBuild'];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function onMainMenuBuild(ConfigureMenuEvent $event)
    {
        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->getRepository()->getPermissionResolver();

        if ($permissionResolver->hasAccess('rss', 'edit')) {
            $this->addSubMenu($event->getMenu());
        }
    }

    /**
     * @return Repository
     */
    public function getRepository(): Repository
    {
        return $this->repository;
    }

    /**
     * Adds the RSS submenu to eZ Platform admin interface.
     *
     * @param \Knp\Menu\ItemInterface $menu
     */
    private function addSubMenu(ItemInterface $menu)
    {
        $menu
            ->addChild('rss', ['route' => 'platform_admin_ui_rss_feeds_list'])
            ->setLabel('ez_rss_feed.menu.main_menu.header');
    }
}
