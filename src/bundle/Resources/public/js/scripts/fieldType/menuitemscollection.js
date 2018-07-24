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
            this.items = this.parseJSON(input.value);
        }

        parseJSON(json) {
            let items = [];
            const parsedItems = JSON.parse(json);
            for (let parsedItem of parsedItems) {
                items.push({
                    'id': parsedItem['id'],
                    'parent': parsedItem['parentId'] || '#',
                    'text': parsedItem['name'],
                    'data': {
                        'position': parsedItem['position'],
                        'url': parsedItem['url'],
                        'target': parsedItem['target'],
                        'menuId': parsedItem['menuId']
                    },
                    'state': {
                        'disabled': false,
                        'opened': true,
                    }
                });
            }

            return items;
        }

        init() {
            let onReadyFunc = this.onReady.bind(this),
                syncItemsFunc = this.syncItems.bind(this);
            this.tree = $(this.treeWrapper)
                .on("ready.jstree", onReadyFunc)
                .on("ready.changed", onReadyFunc)

                .on('enable_node.jstree', syncItemsFunc)
                .on('disabled_node.jstree', syncItemsFunc)
                .on('move_node.jstree', syncItemsFunc)
                .on('copy_node.jstree', syncItemsFunc)
                .on('delete_node.jstree', syncItemsFunc)
                .on('create_node.jstree', syncItemsFunc)
                .jstree({
                    'core': {
                        'data': this.items,
                        'check_callback': this.check.bind(this)
                    },
                    "conditionalselect": function (node, event) {
                        return false;
                    },
                    "contextmenu": {
                        "items": this.getContextmenuItems.bind(this)
                    },
                    "plugins": ["changed", "dnd", "conditionalselect", "contextmenu"]
                })
                .jstree(true);
        }

        getContextmenuItems(node) {
            let parent = this.getNode(node.parent),
                items = {};

            if (node.state.disabled && !parent.state.disabled) {
                items["restoreItem"] = {
                    label: Translator.trans('menu_item.restore'),
                    action: this.onItemRestore.bind(this)
                }
            } else if (!node.state.disabled) {
                items["removeItem"] = {
                    label: Translator.trans('menu_item.remove'),
                    action: this.onItemDelete.bind(this)
                }
            }
            return items;
        }

        onItemRestore(event) {
            this.enableTree(event.reference);
            this.syncItems();
        }

        onItemDelete(event) {
            this.disableTree(event.reference);
            this.syncItems();
        }

        check(operation, node, node_parent, node_position, more) {
            if (operation === "move_node") {
                if (node.state.disabled) return false;
            }
            return true;
        }

        onReady() {

        }

        syncItems() {
            const tree = this.tree.get_json();
            this.items = [];

            for (let index in tree)
                this.addNodeItem(index, tree[index]);

            this.updateInput();
        }

        updateInput() {

            let json = [];
            for (let item of this.items) {
                json.push({
                    "id": item.id,
                    "name": item.text,
                    "parentId": item.parent,
                    "menuId": item.data.menuId,
                    "position": item.data.position,
                    "url": item.data.url,
                    "target": item.data.target
                })
            }
            this.input.value = JSON.stringify(json);
        }


        addNodeItem(index, node, parentNode = null) {
            if (node.state.disabled) return false;
            this.items.push({
                'id': node.id,
                'parent': parentNode ? parentNode.id : null,
                'text': node.text,
                'data': {
                    'position': index,
                    'url': node.data.url,
                    'menuId': node.data.menuId,
                    'target': node.data.target
                },
                'state': node.state
            });

            for (let childIndex in node.children)
                this.addNodeItem(childIndex, node.children[childIndex], node);
        };

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

        enableNode(id) {
            let node = this.tree.get_node(id);
            this.tree.enable_node(node);
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

        enableTree(id) {
            let node = this.enableNode(id);
            if (node && node.children.length > 0) {
                for (let childId of node.children) {
                    this.enableTree(childId);
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

