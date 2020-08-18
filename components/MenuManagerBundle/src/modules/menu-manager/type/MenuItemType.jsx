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

import MenuItem from '../entity/MenuItem'

export default class MenuItemType {
  /**
     * @returns {{max_depth: number, icon: string, max_children: number, valid_children: number}}
     */
  getTreeType () {
    return {
      icon: 'oi oi-link-intact',
      max_children: -1,
      max_depth: -1,
      valid_children: -1
    }
  }

  constructor (identifier, type) {
    this.identifier = identifier
    this.type = type
  }

  /**
     * @param {MenuTreeView} treeView
     * @param node
     * @param {MenuItem} item
     * @returns {{label: string, action:function}}
     */
  getContextualMenu (treeView, node, item) {
    const parent = treeView.getNode(node.parent)
    const isActive = item.getOption('active', true)
    const items = {}

    if (isActive) {
      items['desactivateItem'] = {
        label: Translator.trans('menu_item.action.desactivate'),
        action: (event) => {
          const selectedNode = treeView.getNode(event.reference)
          selectedNode.data.options['active'] = false
          treeView.onTreeChange()
        }
      }
    } else {
      items['activateItem'] = {
        label: Translator.trans('menu_item.action.activate'),
        action: (event) => {
          const selectedNode = treeView.getNode(event.reference)
          selectedNode.data.options['active'] = true
          treeView.onTreeChange()
        }
      }
    }
    if (node.state.disabled && !parent.state.disabled) {
      items['restoreItem'] = {
        label: Translator.trans('menu_item.action.restore'),
        action: (event) => {
          treeView.enableTree(event.reference)
          treeView.onTreeChange()
        }
      }
    } else {
      items['removeItem'] = {
        label: Translator.trans('menu_item.action.remove'),
        action: (event) => {
          treeView.disableTree(event.reference)
          treeView.onTreeChange()
        }
      }
    }
    return items
  }

  /**
     * @param {MenuTreeView} treeView
     * @param parentNode
     * @returns {null}
     */
  getContextualMenuCreateBtn (treeView, parentNode) {
    return null
  }

  /**
     * @param props
     * @returns {MenuItem}
     */
  getNewItem (props) {
    const defaultProps = {
      id: null,
      name: Translator.trans('menu_item.default_title'),
      type: this.type
    }
    props = Object.assign(defaultProps, props || {})
    return new MenuItem(props)
  }
}
