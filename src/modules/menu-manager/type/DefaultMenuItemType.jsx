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

import React from 'react'
import MenuItemEditForm from '../components/form/MenuItemEditFormModule'
import MenuItemType from './MenuItemType'

export default class DefaultMenuItemType extends MenuItemType {
  /**
     * @inheritDoc
     */
  getContextualMenu (treeView, node, item) {
    const items = {}

    items['editItem'] = {
      label: Translator.trans('menu_item.action.edit'),
      action: treeView.handleEditTreeNode,
      separator_after: true
    }
    return Object.assign(items, super.getContextualMenu(treeView, node, item) || {})
  }

  /**
     * @inheritDoc
     * @returns {{action: action, label: *}}
     */
  getContextualMenuCreateBtn (treeView, parentNode) {
    const getNewItem = this.getNewItem.bind(this)
    return {
      label: Translator.trans(`menu_item.action.create.${this.identifier}`),
      action: () => {
        const item = getNewItem({ parentId: parentNode.id })
        treeView.handleCreateTreeNode(item)
      }
    }
  }

  /**
     * @param item
     * @param onSubmit
     * @param onCancel
     * @returns {React.CElement<Readonly<{children?: React.ReactNode}> & Readonly<P>, MenuItemEditFormModule>}
     */
  getEditForm (item, onSubmit, onCancel) {
    return React.createElement(MenuItemEditForm, {
      item: item,
      onSubmit: onSubmit,
      onCancel: onCancel
    })
  }
}
