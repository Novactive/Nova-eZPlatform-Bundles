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
import PropTypes from 'prop-types'
import MenuItemEditFormModule from './MenuItemEditFormModule'

export default class ContainerMenuItemEditFormModule extends MenuItemEditFormModule {
  constructor (props) {
    super(props)
    this.defaultLanguage = ibexa.adminUiConfig.languages.priority[0]
    this.state = {
      name: this.props.item.name,
      language: this.defaultLanguage
    }
    this.handleSubmit = this.handleSubmit.bind(this)
  }

  componentDidUpdate (prevProps, prevState) {
    if (prevProps.item !== this.props.item) {
      this.setState((state) => ({
        name: this.props.item.name
      }))
    }
  }

  handleSubmit (event) {
    event.preventDefault()
    const item = this.props.item
    item.name = this.state.name
    this.props.onSubmit(item)
  }

  render () {
    const languages = []
    for (const languageCode in ibexa.adminUiConfig.languages.mappings) {
      languages.push(ibexa.adminUiConfig.languages.mappings[languageCode])
    }
    return (
      <div>
        <form onSubmit={this.handleSubmit}>
          <div className="form-group mb-3">
            <label className="form-label" htmlFor="item_language">{Translator.trans('menu_item.property.language')}</label>
            <select
              className="form-select form-control"
              id="item_language"
              onChange={this.handleLanguageChange}
              value={this.state.language}
            >
              {[...languages].map((language) => (
                <option key={language.id} value={language.languageCode}>
                  {language.name}
                </option>
              ))}
            </select>
          </div>
          <div className="form-group mb-3">
            <label className="form-label" htmlFor="item_name">
              {Translator.trans('menu_item.property.container_name')} ({this.state.language})
            </label>
            <input
              type="text"
              className="form-control"
              name="name"
              value={this.getInputValue(this.state.name)}
              id="item_name"
              onChange={this.handleLocalizedInputChange}
            />
          </div>
          <button type="submit" className="btn btn-primary pull-right">
            {Translator.trans('menu_item.edit_form.save_container')}
          </button>
          <button type="button" className="btn btn-secondary" onClick={this.handleCancel}>
            {Translator.trans('menu_item.edit_form.cancel')}
          </button>
        </form>
      </div>
    )
  }
}

ContainerMenuItemEditFormModule.propTypes = {
  item: PropTypes.object,
  onSubmit: PropTypes.func,
  onCancel: PropTypes.func
}
