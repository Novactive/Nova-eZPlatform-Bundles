/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */

import React from 'react';
import ContainerMenuItemEditFormModule from '../components/form/container.menu.item.edit.form.module';
import DefaultMenuItemType from './default.menu.item.type.module';
import MenuItem from '../entity/menu.item.entity';

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

    /**
     * @param props
     * @returns {MenuItem}
     */
    getNewItem(props) {
        const defaultProps = {
            id: null,
            name: Translator.trans('menu_item.default_container_title'),
            type: this._type,
        };
        props = Object.assign(defaultProps, props || {});
        return new MenuItem(props);
    }
}
