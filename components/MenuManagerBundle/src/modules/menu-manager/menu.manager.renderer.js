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

import ReactDOM from 'react-dom'
import React from 'react'
import MenuManager from './MenuManagerModule'
import ContainerMenuItemType from './type/ContainerMenuItemType'
import ContentMenuItemType from './type/ContentMenuItemType'
import DefaultMenuItemType from './type/DefaultMenuItemType'

(function (global, doc) {
  const MenuManagerConfig = {
    config: {
      types: [],
      jsTreeTypes: {
        root: {
          icon: 'oi oi-menu'
        }
      }
    },
    registerType: (itemType) => {
      MenuManagerConfig.config['types'].push(itemType)
      MenuManagerConfig.config['jsTreeTypes'][itemType.type] = itemType.getTreeType()
    },
    getTypesMap: () => {
      const map = new Map()
      for (const itemType of MenuManagerConfig.config.types) {
        map.set(itemType.type, itemType)
      }
      return map
    },
    getJsTreeTypes: () => {
      return MenuManagerConfig.config.jsTreeTypes
    }
  }
  const MenuManagerRenderer = {
    render: (container, input, menuRootLocationInput) => {
      const token = doc.querySelector('meta[name="CSRF-Token"]').content
      const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content
      const renderComponent = () => {
        ReactDOM.render(
          React.createElement(MenuManager, {
            loadJson: () => {
              return input.value
            },
            onChange: (items) => {
              const json = []
              for (const item of items.values()) {
                if (!item.isEnabled()) {
                  continue
                }
                json.push({
                  id: item.id,
                  name: item.name,
                  parentId: item.parentId !== '#' ? item.parentId : null,
                  position: item.position,
                  url: item.url,
                  target: item.target,
                  type: item.type,
                  options: item.options
                })
              }
              input.value = JSON.stringify(json)
            },
            config: MenuManagerConfig,
            restInfo: { token, siteaccess },
            treeRootLocationId: menuRootLocationInput.value
          }),
          container
        )
      }

      renderComponent()
      menuRootLocationInput.addEventListener('change', () => {
        renderComponent()
      })
    }
  }

  MenuManagerConfig.registerType(
    new ContainerMenuItemType('container', 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem\\ContainerMenuItem')
  )
  MenuManagerConfig.registerType(new ContentMenuItemType('content', 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem\\ContentMenuItem'))
  MenuManagerConfig.registerType(new DefaultMenuItemType('default', 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem'))

  global['Novactive'] = global.Novactive || {}
  global.Novactive['MenuManagerRenderer'] = MenuManagerRenderer
  global.Novactive['MenuManagerConfig'] = MenuManagerConfig

  const SELECTOR_FORM = '.menu-edit-form'
  const SELECTOR_MENU_MANAGER = '.menu-manager'
  const SELECTOR_INPUT = '.ez-data-source__input'

  document.querySelectorAll(SELECTOR_FORM).forEach((formContainer) => {
    global.Novactive.MenuManagerRenderer.render(
      formContainer.querySelector(SELECTOR_MENU_MANAGER),
      formContainer.querySelector(SELECTOR_INPUT),
      formContainer.querySelector('input[name="menu[rootLocationId]"]')
    )
  })
})(window, window.document)
