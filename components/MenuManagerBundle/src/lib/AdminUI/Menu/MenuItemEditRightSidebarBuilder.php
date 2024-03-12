<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\AdminUI\Menu;

use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

class MenuItemEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    public const ITEM__EDIT = 'menu.edit_form.save';
    public const ITEM__CANCEL = 'menu.edit_form.back';
    public const MENU_MANAGER_EDIT_SIDEBAR_RIGHT = 'ibexa_admin_ui.menu_configure.menu_manager_item_edit_sidebar_right';

    /**
     * @return Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            ( new Message(self::ITEM__EDIT, 'menu_manager') )->setDesc('Publish'),
            ( new Message(self::ITEM__CANCEL, 'menu_manager') )->setDesc('Discard changes'),
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
                            'data-click' => '#menu_item_edit_edit',
                        ],
                        'label' => self::ITEM__EDIT,
                        'extras' => [
                            'translation_domain' => 'menu_manager',
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
                        'route' => 'menu_manager.menu_list',
                        'label' => self::ITEM__CANCEL,
                        'extras' => [
                            'translation_domain' => 'menu_manager',
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
        return self::MENU_MANAGER_EDIT_SIDEBAR_RIGHT;
    }
}
