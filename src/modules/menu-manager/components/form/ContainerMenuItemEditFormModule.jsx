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
import { Button, Form, FormGroup, Input, Label } from 'reactstrap'

export default class ContainerMenuItemEditFormModule extends MenuItemEditFormModule {
  constructor (props) {
    super(props)
    this.defaultLanguage = eZ.adminUiConfig.languages.priority[0]
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
    for (const languageCode in eZ.adminUiConfig.languages.mappings) {
      languages.push(eZ.adminUiConfig.languages.mappings[languageCode])
    }
    return (
      <div>
        <Form onSubmit={this.handleSubmit}>
          <FormGroup>
            <Label for="language">{Translator.trans('menu_item.property.language')}</Label>
            <Input type="select" onChange={this.handleLanguageChange}>
              {[...languages].map((language) => (
                <option key={language.id} value={language.languageCode}>
                  {language.name}
                </option>
              ))}
            </Input>
          </FormGroup>
          <FormGroup>
            <Label for="item_name">
              {Translator.trans('menu_item.property.container_name')} ({this.state.language})
            </Label>
            <Input
              type="text"
              name="name"
              value={this.getInputValue(this.state.name)}
              id="item_name"
              onChange={this.handleLocalizedInputChange}
            />
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

ContainerMenuItemEditFormModule.propTypes = {
  item: PropTypes.object,
  onSubmit: PropTypes.func,
  onCancel: PropTypes.func
}
