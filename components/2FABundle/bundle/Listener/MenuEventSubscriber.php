<?php

/**
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Maxim Strukov <maxim.strukov@almaviacx.com>
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle\Listener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Knp\Menu\Util\MenuManipulator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MenuEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private MenuManipulator $menuManipulator, private TranslatorInterface $translator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        if (PHP_VERSION_ID < 70400) {
            return [];
        }

        return [
            ConfigureMenuEvent::USER_MENU => ['onConfigureUserMenu', -200],
        ];
    }

    public function onConfigureUserMenu(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $newItem = $menu->addChild(
            'user__setup_2fa',
            ['label' => $this->translator->trans('menu_label', [], 'novaez2fa'), 'route' => '2fa_setup']
        );

        $this->menuManipulator->moveToPosition($newItem, count($menu->getChildren()) - 2);
    }
}
