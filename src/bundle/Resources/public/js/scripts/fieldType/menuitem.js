/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

(function (global, $) {
    const SELECTOR_FIELD = '.ez-field-edit--menuitem';
    const SELECT_MENU_WRAPPER = '.menu__wrapper';
    const SELECT_MENU_TREE_WRAPPER = '.menu-tree__wrapper';
    const SELECTOR_INPUT = '.ez-data-source__input';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';
    const SELECTOR_COLLAPSE = '.collapse';

    class MenuItemValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzStringValidator
         */
        validateInput(event) {
            const label = event.target.closest(SELECTOR_FIELD).querySelector('.ez-field-edit__label').innerHTML;
            const isRequired = event.target.required;
            const isEmpty = !event.target.value;
            let hasCorrectValues = true;
            try {
                JSON.parse(event.target.value);
            } catch (e) {
                console.log("Parsing error:", e);
                hasCorrectValues = false;
            }
            const result = {isError: false};

            if (isRequired && isEmpty) {
                result.isError = true;
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isEmpty && !hasCorrectValues) {
                result.isError = true;
                result.errorMessage = global.eZ.errors.invalidValue.replace('{fieldName}', label);
            }

            return result;
        }
    };

    class MenuItem {
        /**
         * @param id int
         * @param menuId int
         * @param parentId int
         * @param position int
         * @param enabled bool
         */
        constructor(id, menuId, parentId, position, enabled) {
            this.id = id;
            this.menuId = menuId;
            this.parentId = parentId;
            this.position = position;
            this.enabled = enabled || true;
        }

        enable() {
            this.enabled = true;
        }

        disable() {
            this.enabled = false;
        }
    }

    class Menu {
        /**
         * @param id int
         * @param tree array
         * @param enabled bool
         */
        constructor(id, tree, enabled) {
            this.id = id;
            this.enabled = enabled || true;
            this.items = new Map();
            this.tree = tree;
        }

        /**
         * @param item MenuItem
         */
        addItem(item) {
            let key = this.id + "-" + item.parentId;
            this.items.set(key, item);
        }

        /**
         * @param parentId int
         * @returns {MenuItem}
         */
        getItemByParentId(parentId) {
            let key = this.id + "-" + parentId;
            return this.items.get(key);
        }

        getItems() {
            return this.items.values();
        }

        hasItems() {
            return this.items.size > 0;
        }

        /**
         * @param parentId int
         */
        enableItem(parentId) {
            let menuItem = this.getItemByParentId(parentId);
            if (!menuItem) {
                this.addItem(new MenuItem(null, this.id, parentId, null, true));
            } else {
                menuItem.enable();
            }
        }

        /**
         * @param parentId int
         */
        disableItem(parentId) {
            let menuItem = this.getItemByParentId(parentId);
            if (menuItem) {
                menuItem.disable();
            }
        }

        enable() {
            this.enabled = true;
        }

        disable() {
            this.enabled = false;
        }
    }

    document.querySelectorAll(SELECTOR_FIELD).forEach(fieldContainer => {
        let menuList = new Map();
        const rawMenuItems = JSON.parse(fieldContainer.querySelector(SELECTOR_INPUT).value);
        const validator = new MenuItemValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: SELECTOR_INPUT,
                    eventName: 'blur',
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER]
                },
            ],
        });

        const updateInput = () => {
            let enabledItems = [];
            for (let menu of menuList.values()) {
                if (menu.enabled) {
                    for (let menuItem of menu.getItems()) {
                        if (menuItem.enabled) enabledItems.push(menuItem);
                    }
                }
            }
            fieldContainer.querySelector(SELECTOR_INPUT).value = JSON.stringify(enabledItems);
        }

        fieldContainer.querySelectorAll(SELECT_MENU_WRAPPER).forEach(menuWrapper => {
            let menu = new Menu(parseInt(menuWrapper.querySelector(SELECT_MENU_TREE_WRAPPER).dataset.menu_id), JSON.parse(menuWrapper.querySelector(SELECT_MENU_TREE_WRAPPER).innerHTML));
            menuList.set(menu.id, menu);
            for (let rawMenuItem of rawMenuItems) {
                if (rawMenuItem['menuId'] === menu.id) {
                    menu.addItem(new MenuItem(rawMenuItem['id'], rawMenuItem['menuId'], rawMenuItem['parentId'] || 0, rawMenuItem['position'], true));
                }
            }

            let tree = $(SELECT_MENU_TREE_WRAPPER, menuWrapper)
                .on("ready.jstree", function () {
                    for (let menuItem of menu.getItems()) {
                        tree.select_node(menuItem.parentId);
                    }

                    $(SELECTOR_COLLAPSE, menuWrapper)
                        .on("show.bs.collapse", function () {
                            menu.enable();
                            $('[data-target="#' + $(this).attr('id') + '"]').prop('checked', true);
                            updateInput();
                        })
                        .on("hide.bs.collapse", function () {
                            menu.disable();
                            $('[data-target="#' + $(this).attr('id') + '"]').prop('checked', false);
                            updateInput();
                        })
                        .collapse(menu.hasItems() ? 'show' : 'hide');

                    $(this).on("changed.jstree", function (e, data) {
                        let tree = $(SELECT_MENU_TREE_WRAPPER, menuWrapper).jstree(true);
                        for (let parentId of data.changed.selected) {
                            menu.enableItem(parentId);
                        }
                        for (let parentId of data.changed.deselected) {
                            menu.disableItem(parentId);
                        }
                        updateInput();
                    });
                    menuWrapper.classList.remove("ez-visually-hidden");

                })
                .jstree({
                    'core': {
                        'data': menu.tree
                    },
                    "checkbox": {
                        "three_state": false
                    },
                    "plugins": ["checkbox", "changed"]
                })
                .jstree(true);

        });
        validator.init();
        global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
            [...global.eZ.fieldTypeValidators, validator] :
            [validator];
    });


})(window, window.jQuery);

