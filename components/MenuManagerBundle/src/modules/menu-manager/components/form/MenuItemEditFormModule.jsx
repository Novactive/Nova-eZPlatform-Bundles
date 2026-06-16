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

import React, { Component } from 'react'
import PropTypes from 'prop-types'

export default class MenuItemEditFormModule extends Component {
  constructor (props) {
    super(props)
    this.defaultLanguage = ibexa.adminUiConfig.languages.priority[0]
    this.state = {
      name: this.props.item.name,
      url: this.props.item.url,
      blank: this.props.item.target === '_blank',
      language: this.defaultLanguage
    }
    this.handleLanguageChange = this.handleLanguageChange.bind(this)
    this.handleInputChange = this.handleInputChange.bind(this)
    this.handleLocalizedInputChange = this.handleLocalizedInputChange.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
    this.handleCancel = this.handleCancel.bind(this)
  }

  componentDidUpdate (prevProps, prevState) {
    if (prevProps.item !== this.props.item) {
      this.setState((state) => ({
        name: this.props.item.name,
        url: this.props.item.url,
        blank: this.props.item.target === '_blank'
      }))
    }
  }

  handleLanguageChange (event) {
    const target = event.target
    const value = target.value
    this.setState({
      language: value
    })
  }

  handleInputChange (event) {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value
    const name = target.name

    this.setState({
      [name]: value
    })
  }

  handleLocalizedInputChange (event) {
    const target = event.target
    const rawValue = target.type === 'checkbox' ? target.checked : target.value
    const name = target.name
    let value = {}
    try {
      value = JSON.parse(this.state[name])
    } catch (e) {
      value[this.defaultLanguage] = this.state[name]
    }
    value[this.state.language] = rawValue
    const stringValue = JSON.stringify(value)
    this.setState({
      [name]: stringValue
    })
  }

  handleSubmit (event) {
    event.preventDefault()
    const item = this.props.item
    item.name = this.state.name
    item.url = this.state.url
    item.target = this.state.blank ? '_blank' : ''
    this.props.onSubmit(item)
  }

  handleCancel (event) {
    this.props.onCancel()
  }

  getInputValue (property) {
    try {
      const values = JSON.parse(property)
      return values[this.state.language] || ''
    } catch (e) {
      return property
    }
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
              {Translator.trans('menu_item.property.name')} ({this.state.language})
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
          <div className="form-group mb-3">
            <label className="form-label" htmlFor="item_url">
              {Translator.trans('menu_item.property.url')} ({this.state.language})
            </label>
            <input
              type="text"
              className="form-control"
              name="url"
              value={this.getInputValue(this.state.url)}
              id="item_url"
              onChange={this.handleLocalizedInputChange}
            />
          </div>
          <div className="form-check mb-3">
            <input
              type="checkbox"
              className="form-check-input"
              name="blank"
              checked={this.state.blank}
              id="item_target"
              onChange={this.handleInputChange}
            />
            <label className="form-check-label" htmlFor="item_target">
              {Translator.trans('menu_item.property.new_window')}
            </label>
          </div>
          <button type="submit" className="btn btn-primary pull-right">
            {Translator.trans('menu_item.edit_form.save')}
          </button>
          <button type="button" className="btn btn-secondary" onClick={this.handleCancel}>
            {Translator.trans('menu_item.edit_form.cancel')}
          </button>
        </form>
      </div>
    )
  }
}

MenuItemEditFormModule.propTypes = {
  item: PropTypes.object,
  onSubmit: PropTypes.func,
  onCancel: PropTypes.func
}
