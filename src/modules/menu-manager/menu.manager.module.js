import React, { Component } from 'react';
import PropTypes from 'prop-types';

import TreeView from './components/tree.view.component';
import MenuItem from './entity/menu.item.entity';

import './css/menu-manager.css';

const DEFAULT_CONFIG = {
    types: {},
};

export default class MenuManagerModule extends Component {
    constructor(props) {
        super(props);
        this.state = {
            items: [],
            editedItem: null,
        };
        this.config = Object.assign({}, DEFAULT_CONFIG, MENU_MANAGER_CONFIG);
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
        let items = new Map();
        for (let parsedItem of parsedItems) {
            const item = new MenuItem({
                id: parsedItem['id'],
                parentId: parsedItem['parentId'] || '#',
                name: parsedItem['name'],
                position: parsedItem['position'],
                url: parsedItem['url'],
                target: parsedItem['target'],
                type: parsedItem['type'],
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
            const typeConfig = this.config.types[this.state.editedItem.type] || {},
                editFormType = typeConfig['edit_form'];

            editForm = React.createElement(MENU_MANAGER_EDIT_FORMS_COMPONENTS[editFormType], {
                item: this.state.editedItem,
                onSubmit: this.handleFormSubmit,
                onCancel: this.handleFormCancel,
            });
        }

        return (
            <div>
                <div className="col-md-12">
                    <TreeView items={this.state.items} onChange={this.handleTreeChange} onEdit={this.handeEdit} types={this.config.types} />
                </div>
                {editForm && <div className="col-md-6 card menu-manager-edit-form-container">{editForm}</div>}
            </div>
        );
    }
}

MenuManagerModule.propTypes = {
    loadJson: PropTypes.func,
    onChange: PropTypes.func,
};
