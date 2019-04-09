import MenuItemType from './menu.item.type.module';

export default class ContentMenuItemType extends MenuItemType {
    /**
     * @inheritDoc
     */
    getTreeType() {
        return {
            icon: 'oi oi-document',
            max_children: -1,
            max_depth: -1,
            valid_children: -1,
        };
    }
}
