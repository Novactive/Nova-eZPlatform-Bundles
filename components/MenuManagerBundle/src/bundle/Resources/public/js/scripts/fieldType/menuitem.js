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

(function (global, $) {
  const SELECTOR_FIELD = '.ibexa-field-edit--menuitem'
  const SELECT_MENU_WRAPPER = '.menu__wrapper'
  const SELECT_MENU_TREE_WRAPPER = '.menu-tree__wrapper'
  const SELECTOR_INPUT = '.ibexa-data-source__input'
  const SELECTOR_LABEL_WRAPPER = '.ibexa-field-edit__label-wrapper'
  const SELECTOR_COLLAPSE = '.collapse'

  class MenuItemValidator extends window.ibexa.BaseFieldValidator {
    /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzStringValidator
         */
    validateInput (event) {
      const label = event.target.closest(SELECTOR_FIELD).querySelector('.ibexa-field-edit__label').innerHTML
      const isRequired = event.target.required
      const isEmpty = !event.target.value
      let hasCorrectValues = true
      try {
        JSON.parse(event.target.value)
      } catch (e) {
        console.log('Parsing error:', e)
        hasCorrectValues = false
      }
      const result = { isError: false }

      if (isRequired && isEmpty) {
        result.isError = true
        result.errorMessage = window.ibexa.errors.emptyField.replace('{fieldName}', label)
      } else if (!isEmpty && !hasCorrectValues) {
        result.isError = true
        result.errorMessage = window.ibexa.errors.invalidValue.replace('{fieldName}', label)
      }

      return result
    }
  }

  class MenuItem {
    /**
         * @param id int
         * @param menuId int
         * @param parentId int
         * @param position int
         * @param isNew bool
         */
    constructor (id, menuId, parentId, position, isNew) {
      this.id = id
      this.menuId = menuId
      this.parentId = parentId
      this.position = position
      this.isNew = isNew || (!id)
      this.enabled = true
    }

    toJSON () {
      return {
        id: this.isNew ? null : this.id,
        menuId: this.menuId,
        parentId: this.parentId,
        position: this.position
      }
    }

    enable () {
      this.enabled = true
    }

    disable () {
      this.enabled = false
    }
  }

  class Menu {
    /**
         * @param id int
         * @param tree array
         * @param enabled bool
         */
    constructor (el, updateInputCallback) {
      this.element = el
      this.id = parseInt(el.querySelector(SELECT_MENU_TREE_WRAPPER).dataset.menu_id)
      this.menuItemName = el.querySelector(SELECT_MENU_TREE_WRAPPER).dataset.menu_item_name
      this.enabled = true
      this.items = new Map()
      this.tree = new MenuTree(el.querySelector(SELECT_MENU_TREE_WRAPPER), this)
      this.updateInputCallback = updateInputCallback

      const defaultParents = el
        .querySelector(SELECT_MENU_TREE_WRAPPER)
        .dataset.default_parents.split(',')
        .filter(Number)
      this.defaultParents = defaultParents.length > 0 ? defaultParents : ['root']
    }

    /**
         * @param item MenuItem
         */
    addItem (item) {
      const key = this.id + '-' + item.parentId
      this.items.set(key, item)
    }

    /**
         * @param parentId int
         * @returns {MenuItem}
         */
    getItemByParentId (parentId) {
      const key = this.id + '-' + parentId
      return this.items.get(key)
    }

    getItems () {
      return this.items.values()
    }

    hasItems () {
      return this.items.size > 0
    }

    /**
         * @param parentId int
         */
    enableItem (parentId) {
      let menuItem = this.getItemByParentId(parentId)
      if (!menuItem) {
        const id = this.tree.createNode(
          {
            id: null,
            text: this.menuItemName,
            state: {
              disabled: true
            },
            type: 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem\\ContentMenuItem'
          },
          parentId,
          'last'
        )

        menuItem = new MenuItem(id, this.id, parentId, null, true)
        this.addItem(menuItem)
      }
      menuItem.enable()
      return menuItem
    }

    /**
         * @param parentId int
         */
    disableItem (parentId) {
      const menuItem = this.getItemByParentId(parentId)
      if (menuItem) {
        menuItem.disable()
      }
      return menuItem
    }

    enable () {
      this.enabled = true
    }

    disable () {
      this.enabled = false
    }

    init () {
      this.tree.init()
      $(this.tree.element).on('ready.jstree', this.onTreeReady.bind(this))
    }

    onTreeReady () {
      const menu = this
      $(SELECTOR_COLLAPSE, this.element)
        .on('show.bs.collapse', function () {
          menu.enable()
          $('[data-target="#' + $(this).attr('id') + '"]').prop('checked', true)
          menu.updateInputCallback()
        })
        .on('hide.bs.collapse', function () {
          menu.disable()
          $('[data-target="#' + $(this).attr('id') + '"]').prop('checked', false)
          menu.updateInputCallback()
        })
        .collapse(this.hasItems() ? 'show' : 'hide')

      if (!this.hasItems()) {
        menu.disable()
        if (this.defaultParents.length > 0) {
          for (const defaultParent of this.defaultParents) {
            this.enableItem(defaultParent)
          }
        }
      }

      for (const menuItem of this.getItems()) {
        this.tree.selectNode(menuItem.parentId)
        this.tree.disableTree(menuItem.id)
      }

      $(this.tree.element).on('changed.jstree', this.onTreeChange.bind(this))
      $(this.tree.element).on('move_node.jstree', this.onTreeChange.bind(this))
      this.element.classList.remove('ibexa-visually-hidden')
    }

    onTreeChange (e, data) {
      if ((e.type === 'changed', data.changed)) {
        for (const parentId of data.changed.selected) {
          this.enableItem(parentId)
          const menuItem = this.getItemByParentId(parentId)
          if (menuItem) {
            this.tree.showNode(menuItem.id)
          }
        }
        for (const parentId of data.changed.deselected) {
          this.disableItem(parentId)
          const menuItem = this.getItemByParentId(parentId)
          if (menuItem) {
            this.tree.hideNode(menuItem.id)
          }
        }
      }

      this.updatePositions()
      this.updateInputCallback()
    }

    updatePositions () {
      const menuItems = this.getItems()
      for (const menuItem of menuItems) {
        const parentNode = this.tree.getNode(menuItem.parentId)
        menuItem.position = parentNode.children.findIndex((id) => {
          return id === menuItem.id
        })
      }
    }
  }

  class MenuTree {
    constructor (el, menu) {
      this.element = el
      this.tree = null
      this.json = JSON.parse(el.innerHTML)
      this.menu = menu
    }

    init () {
      this.tree = $(this.element)
        .on('ready.jstree', this.onReady.bind(this))
        .jstree({
          core: {
            data: this.json,
            check_callback: this.check.bind(this)
          },
          types: global.Novactive.MenuManagerConfig.getJsTreeTypes(),
          checkbox: {
            three_state: false
          },
          plugins: ['checkbox', 'changed', 'types']
        })
        .jstree(true)
    }

    check (operation, node, parentNode, nodePosition, more) {
      if (operation === 'create_node') return true
      if (operation === 'move_node') {
        const menuItem = this.menu.getItemByParentId(node.parent)
        if (menuItem && menuItem.id === node.id && node.parent === parentNode.id && menuItem.isNew) return true
      }
      return false
    }

    onReady () {}

    getNode (id) {
      return this.tree.get_node(id)
    }

    showNode (id) {
      const node = this.tree.get_node(id)
      this.tree.show_node(node)
      return node
    }

    hideNode (id) {
      const node = this.tree.get_node(id)
      this.tree.hide_node(node)
      return node
    }

    disableNode (id) {
      const node = this.tree.get_node(id)
      this.tree.disable_node(node)
      return node
    }

    disableTree (id) {
      const node = this.disableNode(id)
      if (node && node.children.length > 0) {
        this.disableNode(node.parent)
        for (const childId of node.children) {
          this.disableTree(childId)
        }
      }
    }

    createNode (node, parentId, pos) {
      return this.tree.create_node(parentId, node, pos)
    }

    selectNode (menuItemId) {
      this.tree.select_node(menuItemId)
    }
  }

  document.querySelectorAll(SELECTOR_FIELD).forEach((fieldContainer) => {
    const menuList = new Map()
    const rawMenuItems = JSON.parse(fieldContainer.querySelector(SELECTOR_INPUT).value)
    const validator = new MenuItemValidator({
      classInvalid: 'is-invalid',
      fieldContainer,
      eventsMap: [
        {
          selector: SELECTOR_INPUT,
          eventName: 'blur',
          callback: 'validateInput',
          errorNodeSelectors: [SELECTOR_LABEL_WRAPPER]
        }
      ]
    })

    const updateInput = () => {
      const enabledItems = []
      for (const menu of menuList.values()) {
        if (menu.enabled) {
          for (const menuItem of menu.getItems()) {
            if (menuItem.enabled) enabledItems.push(menuItem)
          }
        }
      }
      fieldContainer.querySelector(SELECTOR_INPUT).value = JSON.stringify(enabledItems)
    }

    fieldContainer.querySelectorAll(SELECT_MENU_WRAPPER).forEach((menuWrapper) => {
      const menu = new Menu(menuWrapper, updateInput)
      menuList.set(menu.id, menu)
      for (const rawMenuItem of rawMenuItems) {
        if (rawMenuItem['menuId'] === menu.id) {
          menu.addItem(
            new MenuItem(
              rawMenuItem['id'],
              rawMenuItem['menuId'],
              rawMenuItem['parentId'] || 'root',
              rawMenuItem['position'],
              false
            )
          )
        }
      }
      menu.init()
    })
    validator.init()
    window.ibexa.fieldTypeValidators = window.ibexa.fieldTypeValidators ? [...window.ibexa.fieldTypeValidators, validator] : [validator]
  })
})(window, window.jQuery)
