import React from 'react';
import MenuItemEditForm from '../components/form/menu.item.edit.form.module';
import MenuItemType from './menu.item.type.module';

export default class DefaultMenuItemType extends MenuItemType {
    /**
     * @inheritDoc
     */
    getContextualMenu(treeView, node) {
        let items = {};

        items['editItem'] = {
            label: Translator.trans('menu_item.action.edit'),
            action: treeView.handleEditTreeNode,
            separator_after: true,
        };
        return Object.assign(items, super.getContextualMenu(treeView, node) || {});
    }

    /**
     * @inheritDoc
     * @returns {{action: action, label: *}}
     */
    getContextualMenuCreateBtn(treeView, parentNode) {
        const getNewItem = this.getNewItem.bind(this);
        return {
            label: Translator.trans(`menu_item.action.create.${this.identifier}`),
            action: () => {
                const item = getNewItem({ parentId: parentNode.id });
                this.handleCreateTreeNode(item);
            },
        };
    }

    /**
     * @param item
     * @param onSubmit
     * @param onCancel
     * @returns {React.CElement<Readonly<{children?: React.ReactNode}> & Readonly<P>, MenuItemEditFormModule>}
     */
    getEditForm(item, onSubmit, onCancel) {
        return React.createElement(MenuItemEditForm, {
            item: item,
            onSubmit: onSubmit,
            onCancel: onCancel,
        });
    }
}
