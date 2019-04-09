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

import ReactDOM from 'react-dom';
import React from 'react';
import MenuManager from './menu.manager.module';
import ContainerMenuItemType from './type/container.menu.item.type.module';
import ContentMenuItemType from './type/content.menu.item.type.module';
import DefaultMenuItemType from './type/default.menu.item.type.module';

(function(global) {
    const MenuManagerConfig = {
        config: {
            types: [],
        },
        registerType: (type) => {
            MenuManagerConfig.config['types'].push(type);
        },
        getTypesMap: () => {
            let map = new Map();
            for (const itemType of MenuManagerConfig.config.types) {
                map.set(itemType.type, itemType);
            }
            return map;
        },
    };
    const MenuManagerRenderer = {
        render: (container, input) => {
            ReactDOM.render(
                React.createElement(MenuManager, {
                    loadJson: () => {
                        return input.value;
                    },
                    onChange: (items) => {
                        let json = [];
                        for (let item of items.values()) {
                            if (!item.isEnabled()) continue;
                            json.push({
                                id: item.id,
                                name: item.name,
                                parentId: item.parentId !== '#' ? item.parentId : null,
                                position: item.position,
                                url: item.url,
                                target: item.target,
                                type: item.type,
                                options: item.options,
                            });
                        }
                        input.value = JSON.stringify(json);
                    },
                    config: MenuManagerConfig,
                }),
                container
            );
        },
    };

    MenuManagerConfig.registerType(
        new ContainerMenuItemType('container', 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem\\ContainerMenuItem')
    );
    MenuManagerConfig.registerType(new ContentMenuItemType('content', 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem\\ContentMenuItem'));
    MenuManagerConfig.registerType(new DefaultMenuItemType('default', 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem'));

    global['Novactive'] = global.Novactive || {};
    global.Novactive['MenuManagerRenderer'] = MenuManagerRenderer;
    global.Novactive['MenuManagerConfig'] = MenuManagerConfig;
})(window);
