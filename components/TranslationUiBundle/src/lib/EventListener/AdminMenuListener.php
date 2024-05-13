<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaTranslationUi\EventListener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminMenuListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', 0],
        ];
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $menu[MainMenuBuilder::ITEM_CONTENT]->addChild(
            'translation_manager_translations_list',
            [
                'label' => 'ibexa.translation.ui.label',
                'route' => 'lexik_translation_overview',
            ]
        );
    }
}
