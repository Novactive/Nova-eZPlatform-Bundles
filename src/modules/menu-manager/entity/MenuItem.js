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

export default class MenuItem {
  constructor (props) {
    this.id = String(
      props.id ||
                '_' +
                    Math.random()
                      .toString(36)
                      .substr(2, 9)
    )
    this.parentId = props.parentId || '#'
    this.name = props.name
    this.position = props.position || 0
    this.url = props.url || ''
    this.target = props.target || ''
    this.state = props.state
    this.type = props.type
    this.options = props.options || {}
  }

  getOption (name, defaultValue = null) {
    const option = this.options[name]
    return option !== undefined ? option : defaultValue
  }

  setOption (name, value) {
    this.options[name] = value
  }

  toTreeNode (language) {
    const isActive = this.getOption('active', true)
    let className = ''
    if (isActive === false) {
      className += 'jstree-desactivated'
    }
    return {
      id: this.id,
      parent: this.parentId,
      text: this.translateProperty(this.name, language),
      data: {
        position: this.position,
        url: this.url,
        target: this.target,
        options: this.options
      },
      state: this.state,
      type: this.type,
      a_attr: {
        class: className
      }
    }
  }

  translateProperty (property, language) {
    try {
      const values = JSON.parse(property)
      return values[language]
    } catch (e) {
      return property
    }
  }

  isEnabled () {
    return this.state === undefined || !this.state.disabled
  }

  static fromTreeNode (node, position = null) {
    return new MenuItem({
      id: node.id,
      parentId: node.parent,
      name: node.text,
      position: position || node.data.position,
      url: node.data.url,
      target: node.data.target,
      options: node.data.options,
      state: node.state,
      type: node.type
    })
  }
}
