<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\AdminUi\Menu\Event;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuListener implements EventSubscriberInterface, TranslationContainerInterface
{
    public function __construct(protected PermissionResolver $permissionResolver)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', -1000],
        ];
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'workflow.list')) {
            return;
        }

        $menu = $event->getMenu();

        $contentMenu = $menu->getChild(MainMenuBuilder::ITEM_CONTENT);

        $importExportGroup = $contentMenu->addChild(
            'export_import',
            [
                'extras' => [
                    'orderNumber' => 1000,
                ],
            ]
        );
        $importExportGroup->addChild(
            'export_import_job_list',
            [
                'route' => 'import_export.job.list',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message('export_import', 'ibexa_menu'))->setDesc('Import / Export'),
            (new Message('export_import_job_list', 'ibexa_menu'))->setDesc('Jobs'),
        ];
    }
}
