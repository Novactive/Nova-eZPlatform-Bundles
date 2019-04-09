import React from 'react';
import ContainerMenuItemEditFormModule from '../components/form/container.menu.item.edit.form.module';
import DefaultMenuItemType from './default.menu.item.type.module';

export default class ContainerMenuItemType extends DefaultMenuItemType {
    /**
     * @inheritDoc
     */
    getTreeType() {
        return {
            icon: 'oi oi-folder',
            max_children: -1,
            max_depth: -1,
            valid_children: -1,
        };
    }

    /**
     * @inheritDoc
     */
    getEditForm(item, onSubmit, onCancel) {
        const form = React.createElement(ContainerMenuItemEditFormModule, {
            item: item,
            onSubmit: onSubmit,
            onCancel: onCancel,
        });
        return form;
    }
}
