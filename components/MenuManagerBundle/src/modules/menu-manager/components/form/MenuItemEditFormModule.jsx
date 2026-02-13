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
import { Button, Form, FormGroup, Input, Label } from 'reactstrap'

export default class MenuItemEditFormModule extends Component {
  constructor (props) {
    super(props)
    this.defaultLanguage = eZ.adminUiConfig.languages.priority[0]
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
    for (const languageCode in eZ.adminUiConfig.languages.mappings) {
      languages.push(eZ.adminUiConfig.languages.mappings[languageCode])
    }
    return (
      <div>
        <Form onSubmit={this.handleSubmit}>
          <FormGroup>
            <Label for="language">{Translator.trans('menu_item.property.language')}</Label>
            <Input type="select" onChange={this.handleLanguageChange} value={this.state.language}>
              {[...languages].map((language) => (
                <option key={language.id} value={language.languageCode}>
                  {language.name}
                </option>
              ))}
            </Input>
          </FormGroup>
          <FormGroup>
            <Label for="item_name">
              {Translator.trans('menu_item.property.name')} ({this.state.language})
            </Label>
            <Input
              type="text"
              name="name"
              value={this.getInputValue(this.state.name)}
              id="item_name"
              onChange={this.handleLocalizedInputChange}
            />
          </FormGroup>
          <FormGroup>
            <Label for="item_url">
              {Translator.trans('menu_item.property.url')} ({this.state.language})
            </Label>
            <Input
              type="text"
              name="url"
              value={this.getInputValue(this.state.url)}
              id="item_url"
              onChange={this.handleLocalizedInputChange}
            />
          </FormGroup>
          <FormGroup check>
            <Label check for="item_target">
              <Input
                type="checkbox"
                name="blank"
                checked={this.state.blank}
                id="item_target"
                onChange={this.handleInputChange}
              />
              {Translator.trans('menu_item.property.new_window')}
            </Label>
          </FormGroup>
          <Button type="submit" className="pull-right" color="primary">
            {Translator.trans('menu_item.edit_form.save')}
          </Button>
          <Button type="button" onClick={this.handleCancel}>
            {Translator.trans('menu_item.edit_form.cancel')}
          </Button>
        </Form>
      </div>
    )
  }
}

MenuItemEditFormModule.propTypes = {
  item: PropTypes.object,
  onSubmit: PropTypes.func,
  onCancel: PropTypes.func
}
