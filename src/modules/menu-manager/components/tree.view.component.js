import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import $ from 'jquery';
import 'jstree/dist/jstree.min';
import MenuItem from '../entity/menu.item.entity';

const ROOT_ID = 'root';
const DEFAULT_ITEM_TYPES = {
    root: {
        icon: 'oi oi-menu',
    },
};

export default class TreeView extends PureComponent {
    constructor(props) {
        super(props);
        this.tree = null;
        this.types = Object.assign({}, DEFAULT_ITEM_TYPES, this.props.types);
        this.onTreeChange = this.onTreeChange.bind(this);
        this.handleCheck = this.handleCheck.bind(this);
        this.handleContextMenu = this.handleContextMenu.bind(this);
        this.handleDeleteTreeNode = this.handleDeleteTreeNode.bind(this);
        this.handleRestoreTreeNode = this.handleRestoreTreeNode.bind(this);
        this.handleEditTreeNode = this.handleEditTreeNode.bind(this);
        this.handleCreateTreeNode = this.handleCreateTreeNode.bind(this);
    }

    getTreeData() {
        let data = [
            {
                id: ROOT_ID,
                text: Translator.trans('menu.root'),
                parent: '#',
                type: ROOT_ID,
                state: {
                    opened: true,
                },
            },
        ];
        /**
         * @var item MenuItem
         */
        for (let item of this.props.items.values()) {
            const node = item.toTreeNode();
            if (node.parent === '#') node.parent = ROOT_ID;
            data.push(node);
        }
        return data;
    }

    onTreeChange() {
        let items = new Map();
        const tree = this.tree.get_json(null, { flat: true });

        for (const [index, node] of Object.entries(tree)) {
            if (node.id === ROOT_ID) continue;
            if (node.parent === ROOT_ID) node.parent = '#';
            const item = MenuItem.fromTreeNode(node, index);
            items.set(item.id, item);
        }

        this.props.onChange(items);
    }

    handleCheck(operation, node, node_parent, node_position, more) {
        if (operation === 'move_node') {
            if (node.id === ROOT_ID) return false;
            if (node_parent.id === '#') return false;
            if (node.state.disabled) return false;
        }
        return true;
    }

    handleContextMenu(node) {
        const parent = this.tree.get_node(node.parent);
        let items = {};

        if (node.state.disabled && !parent.state.disabled) {
            items['restoreItem'] = {
                label: Translator.trans('menu_item.action.restore'),
                action: this.handleRestoreTreeNode,
            };
        } else if (!node.state.disabled) {
            for (const type in this.types) {
                const typeConfig = this.types[type] || {},
                    editFormType = typeConfig['edit_form'] || null;

                if (editFormType) {
                    items['createItem'] = {
                        label: Translator.trans('menu_item.action.create'),
                        action: (event) => {
                            const parentNode = this.tree.get_node(event.reference),
                                item = new MenuItem({
                                    id: null,
                                    name: Translator.trans('menu_item.default_title'),
                                    parentId: parentNode.id,
                                    type: type,
                                });
                            this.handleCreateTreeNode(item);
                        },
                    };
                }
            }
            if (node.id !== ROOT_ID) {
                const typeConfig = this.types[node.type] || {},
                    editFormType = typeConfig['edit_form'] || null;
                if (editFormType) {
                    items['editItem'] = {
                        label: Translator.trans('menu_item.action.edit'),
                        action: this.handleEditTreeNode,
                    };
                }
                items['removeItem'] = {
                    label: Translator.trans('menu_item.action.remove'),
                    action: this.handleDeleteTreeNode,
                };
            }
        }
        return items;
    }

    handleRestoreTreeNode(event) {
        this.enableTree(event.reference);
        this.onTreeChange();
    }

    handleDeleteTreeNode(event) {
        this.disableTree(event.reference);
        this.onTreeChange();
    }

    handleCreateTreeNode(item) {
        this.props.onEdit(item);
    }

    handleEditTreeNode(event) {
        const node = this.tree.get_node(event.reference),
            item = MenuItem.fromTreeNode(node);
        this.props.onEdit(item);
    }

    disableTree(id) {
        const node = this.disableNode(id);
        if (node && node.children.length > 0) {
            for (let childId of node.children) {
                this.disableTree(childId);
            }
        }
    }

    enableTree(id) {
        const node = this.enableNode(id);
        if (node && node.children.length > 0) {
            for (let childId of node.children) {
                this.enableTree(childId);
            }
        }
    }

    enableNode(id) {
        const node = this.tree.get_node(id);
        this.tree.enable_node(node);
        return node;
    }

    disableNode(id) {
        const node = this.tree.get_node(id);
        this.tree.disable_node(node);
        return node;
    }

    componentDidMount() {
        this.tree = $(this.treeContainer)
            .jstree({
                core: {
                    data: this.getTreeData(),
                    check_callback: this.handleCheck,
                },
                types: this.types,
                conditionalselect: (node, event) => {
                    return false;
                },
                contextmenu: {
                    items: this.handleContextMenu,
                },
                plugins: ['changed', 'dnd', 'conditionalselect', 'contextmenu', 'types'],
            })
            .jstree(true);
        $(this.treeContainer).on('move_node.jstree copy_node.jstree delete_node.jstree create_node.jstree', this.onTreeChange);
    }

    componentDidUpdate() {
        this.tree.settings.core.data = this.getTreeData();
        this.tree.refresh({ skip_loading: true });
    }

    render() {
        return <div ref={(div) => (this.treeContainer = div)} />;
    }
}

TreeView.propTypes = {
    items: PropTypes.arrayOf(PropTypes.instanceOf(MenuItem)),
    types: PropTypes.object,
    onChange: PropTypes.func,
    onEdit: PropTypes.func,
};
