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

import React, { PureComponent } from 'react'
import PropTypes from 'prop-types'

import $ from 'jquery'
import 'jstree/dist/jstree'
import MenuItem from '../entity/MenuItem'

const ROOT_ID = 'root'
export default class MenuTreeView extends PureComponent {
  constructor (props) {
    super(props)
    this.tree = null

    this.onTreeChange = this.onTreeChange.bind(this)
    this.handleCheck = this.handleCheck.bind(this)
    this.getNode = this.getNode.bind(this)
    this.handleContextMenu = this.handleContextMenu.bind(this)
    this.handleEditTreeNode = this.handleEditTreeNode.bind(this)
    this.handleCreateTreeNode = this.handleCreateTreeNode.bind(this)
  }

  getTreeData () {
    const data = [
      {
        id: ROOT_ID,
        text: Translator.trans('menu.root'),
        parent: '#',
        type: ROOT_ID,
        state: {
          opened: true
        }
      }
    ]
    /**
         * @var item MenuItem
         */
    for (const item of this.props.items.values()) {
      const node = item.toTreeNode(this.props.language)
      if (node.parent === '#') {
        node.parent = ROOT_ID
      }
      data.push(node)
    }
    return data
  }

  onTreeChange () {
    const items = new Map()
    const tree = this.tree.get_json(null, { flat: true })

    for (const [index, node] of Object.entries(tree)) {
      if (node.id === ROOT_ID) {
        continue
      }
      if (node.parent === ROOT_ID) {
        node.parent = '#'
      }
      const item = MenuItem.fromTreeNode(node, index)
      items.set(item.id, item)
    }

    this.props.onChange(items)
  }

  handleCheck (operation, node, parentNode, nodePosition, more) {
    if (operation === 'move_node') {
      if (node.id === ROOT_ID) {
        return false
      }
      if (parentNode.id === '#') {
        return false
      }
      if (node.state.disabled) {
        return false
      }
    }
    return true
  }

  handleContextMenu (node) {
    const item = this.props.items.get(node.id)
    let contextMenuItems = {}

    if (!node.state.disabled) {
      const itemTypes = [...this.props.types.values()]
      const submenuItems = {}
      for (const itemType of itemTypes) {
        const btn = itemType.getContextualMenuCreateBtn(this, node, item)
        if (btn) {
          submenuItems[`createItem_${itemType.identifier}`] = btn
        }
      }
      contextMenuItems[`createItem`] = {
        label: Translator.trans(`menu_item.action.create`),
        submenu: submenuItems,
        separator_after: true
      }
    }

    if (node.id !== ROOT_ID) {
      const itemType = this.props.types.get(node.type)
      contextMenuItems = Object.assign(contextMenuItems, itemType.getContextualMenu(this, node, item) || {})
    }
    return contextMenuItems
  }

  handleCreateTreeNode (item) {
    this.props.onEdit(item)
  }

  handleEditTreeNode (event) {
    const node = this.tree.get_node(event.reference)
    const item = this.props.items.get(node.id)
    this.props.onEdit(item)
  }

  getNode (id) {
    return this.tree.get_node(id)
  }

  disableTree (id) {
    const node = this.disableNode(id)
    if (node && node.children.length > 0) {
      for (const childId of node.children) {
        this.disableTree(childId)
      }
    }
  }

  enableTree (id) {
    const node = this.enableNode(id)
    if (node && node.children.length > 0) {
      for (const childId of node.children) {
        this.enableTree(childId)
      }
    }
  }

  enableNode (id) {
    const node = this.tree.get_node(id)
    this.tree.enable_node(node)
    return node
  }

  disableNode (id) {
    const node = this.tree.get_node(id)
    this.tree.disable_node(node)
    return node
  }

  componentDidMount () {
    this.tree = $(this.treeContainer)
      .jstree({
        core: {
          data: this.getTreeData(),
          check_callback: this.handleCheck,
            worker: false
        },
        types: this.props.jsTreeTypes,
        conditionalselect: (node, event) => {
          return false
        },
        contextmenu: {
          items: this.handleContextMenu
        },
        plugins: ['changed', 'dnd', 'conditionalselect', 'contextmenu', 'types']
      })
      .jstree(true)
    $(this.treeContainer).on('move_node.jstree copy_node.jstree delete_node.jstree create_node.jstree', this.onTreeChange)
  }

  componentDidUpdate () {
    this.tree.settings.core.data = this.getTreeData()
    this.tree.refresh({ skip_loading: true })
  }

  render () {
    return <div ref={(div) => (this.treeContainer = div)} />
  }
}

MenuTreeView.propTypes = {
  items: PropTypes.instanceOf(Map),
  types: PropTypes.instanceOf(Map),
  jsTreeTypes: PropTypes.object,
  onChange: PropTypes.func,
  onEdit: PropTypes.func,
  language: PropTypes.string
}
