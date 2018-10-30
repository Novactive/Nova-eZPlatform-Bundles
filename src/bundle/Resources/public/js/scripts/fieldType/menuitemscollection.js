/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

(function(global, React, ReactDOM, $) {
    const SELECTOR_FIELD = '.menu-edit__items-collection';
    const SELECTOR_MENU_MANAGER = '.menu-manager';
    const SELECTOR_INPUT = '.ez-data-source__input';

    document.querySelectorAll(SELECTOR_FIELD).forEach((fieldContainer) => {
        const input = fieldContainer.querySelector(SELECTOR_INPUT);
        ReactDOM.render(
            React.createElement(Novactive.modules.MenuManager, {
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
                        });
                    }
                    input.value = JSON.stringify(json);
                },
            }),
            fieldContainer.querySelector(SELECTOR_MENU_MANAGER)
        );
    });
})(window, window.React, window.ReactDOM, window.jQuery);
