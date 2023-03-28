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

namespace Novactive\EzRssFeedBundle\PlatformAdminUI\Menu;

use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

class RssEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    public const ITEM__EDIT = 'content_edit__sidebar_right__publish';
    public const ITEM__CANCEL = 'content_create__sidebar_right__cancel';
    public const RSS_EDIT_SIDEBAR_RIGHT = 'ibexa_admin_ui.menu_configure.rss_edit_sidebar_right';

    /**
     * @return Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            ( new Message(self::ITEM__EDIT, 'menu') )->setDesc('Publish'),
            ( new Message(self::ITEM__CANCEL, 'menu') )->setDesc('Discard changes'),
        ];
    }

    public function createStructure(array $options): ItemInterface
    {
        /** @var ItemInterface|ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');
        $menu->setChildren(
            [
                self::ITEM__EDIT => $this->createMenuItem(
                    self::ITEM__EDIT,
                    [
                        'attributes' => [
                            'class' => 'ibexa-btn--trigger',
                            'data-click' => '#rss_edit_edit',
                        ],
                        'label' => self::ITEM__EDIT,
                        'extras' => [
                            'icon' => 'publish',
                        ],
                    ]
                ),
                self::ITEM__CANCEL => $this->createMenuItem(
                    self::ITEM__CANCEL,
                    [
                        'attributes' => [
                            'class' => 'ibexa-btn--dark',
                        ],
                        'route' => 'platform_admin_ui_rss_feeds_list',
                        'label' => self::ITEM__CANCEL,
                        'extras' => [
                            'icon' => 'circle-close',
                        ],
                    ]
                ),
            ]
        );

        return $menu;
    }

    protected function getConfigureEventName(): string
    {
        return self::RSS_EDIT_SIDEBAR_RIGHT;
    }
}
