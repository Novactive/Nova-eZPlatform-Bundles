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

import React, { Component } from 'react';
import PropTypes from 'prop-types';

import TreeView from './components/tree.view.component';
import MenuItem from './entity/menu.item.entity';

import './css/menu-manager.css';

export default class MenuManagerModule extends Component {
    constructor(props) {
        super(props);
        this.state = {
            items: new Map(),
            editedItem: null,
        };
        this.handleTreeChange = this.handleTreeChange.bind(this);
        this.handeEdit = this.handeEdit.bind(this);
        this.handleFormSubmit = this.handleFormSubmit.bind(this);
        this.handleFormCancel = this.handleFormCancel.bind(this);
    }

    handleTreeChange(items) {
        this.props.onChange(items);
        this.setState((state) => ({
            items: items,
        }));
    }

    handeEdit(item) {
        this.setState((state) => ({
            editedItem: item,
        }));
    }

    handleFormCancel(item) {
        this.setState((state) => ({
            editedItem: null,
        }));
    }
    handleFormSubmit(item) {
        this.setState((state) => {
            let newItems = new Map(this.state.items);
            newItems.set(item.id, item);
            this.props.onChange(newItems);
            return {
                items: newItems,
                editedItem: null,
            };
        });
    }

    componentDidMount() {
        const json = this.props.loadJson();
        const parsedItems = JSON.parse(json);
        let items = this.state.items;
        for (let parsedItem of parsedItems) {
            const item = new MenuItem({
                id: parsedItem['id'],
                parentId: parsedItem['parentId'] || '#',
                name: parsedItem['name'],
                position: parsedItem['position'],
                url: parsedItem['url'],
                target: parsedItem['target'],
                type: parsedItem['type'],
                options: parsedItem['options'],
            });
            items.set(item.id, item);
        }
        this.setState((state) => ({
            items: items,
        }));
    }
    render() {
        let editForm = null;
        if (this.state.editedItem) {
            const itemType = this.props.config.getTypesMap().get(this.state.editedItem.type);
            editForm = itemType.getEditForm(this.state.editedItem, this.handleFormSubmit, this.handleFormCancel);
        }

        return (
            <div>
                <div className="col-md-12">
                    <TreeView
                        items={this.state.items}
                        onChange={this.handleTreeChange}
                        onEdit={this.handeEdit}
                        language={eZ.adminUiConfig.languages.priority[0]}
                        types={this.props.config.getTypesMap()}
                    />
                </div>
                {editForm && <div className="col-md-6 card menu-manager-edit-form-container">{editForm}</div>}
            </div>
        );
    }
}

MenuManagerModule.propTypes = {
    loadJson: PropTypes.func,
    onChange: PropTypes.func,
    config: PropTypes.object,
};
