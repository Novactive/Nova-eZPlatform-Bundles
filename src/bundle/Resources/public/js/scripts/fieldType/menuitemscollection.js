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
    const SELECTOR_FIELD = '.menu-edit__items-collection';
    const SELECT_MENU_TREE_WRAPPER = '.menu-tree__wrapper';
    const SELECTOR_INPUT = '.ez-data-source__input';

    class MenuTree {
        constructor(treeWrapper, input) {
            this.treeWrapper = treeWrapper;
            this.input = input;
            this.tree = null;
            this.json = this.parseJSON(input.value);
        }

        parseJSON(json) {
            let items = [];
            const parsedItems = JSON.parse(json);
            for (let parsedItem of parsedItems) {
                items.push({
                    'id': parsedItem['id'],
                    'parent': parsedItem['parentId'] || '#',
                    'text': parsedItem['name'],
                    'state': {
                        'disabled': true,
                        'opened': true,
                    }
                });
            }

            return items;
        }

        init() {
            this.tree = $(this.treeWrapper)
                .on("ready.jstree", this.onReady.bind(this))
                .jstree({
                    'core': {
                        'data': this.json,
                        'check_callback': this.check.bind(this)
                    },
                    "plugins": ["changed", "dnd"]
                })
                .jstree(true);
        }

        check(operation, node, node_parent, node_position, more) {
            return true;
            // if(operation === "create_node") return true;
            // if(operation === "move_node"){
            //     let menuItem = this.menu.getItemByParentId(node.parent);
            //     if(menuItem && menuItem.id == node.id && node.parent === node_parent.id && menuItem.isNew) return true;
            // }
            // return false;
        }

        onReady() {

        }

        getNode(id) {
            return this.tree.get_node(id);
        }

        showNode(id) {
            let node = this.tree.get_node(id);
            this.tree.show_node(node);
            return node;
        }

        hideNode(id) {
            let node = this.tree.get_node(id);
            this.tree.hide_node(node);
            return node;
        }

        disableNode(id) {
            let node = this.tree.get_node(id);
            this.tree.disable_node(node);
            return node;
        }

        disableTree(id) {
            let node = this.disableNode(id);
            if (node && node.children.length > 0) {
                for (let childId of node.children) {
                    this.disableTree(childId);
                }
            }
        }

        createNode(node, parentId, pos) {
            return this.tree.create_node(
                parentId,
                node,
                pos
            );
        }

        selectNode(id) {
            this.tree.select_node(id);
        }
    }

    document.querySelectorAll(SELECTOR_FIELD).forEach(fieldContainer => {
        let menuTree = new MenuTree(fieldContainer.querySelector(SELECT_MENU_TREE_WRAPPER), fieldContainer.querySelector(SELECTOR_INPUT));
        menuTree.init();
    });


})(window, window.jQuery);

