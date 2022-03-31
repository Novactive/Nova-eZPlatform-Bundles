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

import React, { PureComponent } from 'react'
import PropTypes from 'prop-types'
import $ from 'jquery'
import 'jstree/dist/jstree'

const NOTIFICATION_ERROR_LABEL = 'error';
const ENDPOINT_LOAD_SUBITEMS = '/api/ezp/v2/location/tree/load-subitems';

const showNotification = (detail) => {
  const event = new CustomEvent('ez-notify', {
    detail,
  });

  document.body.dispatchEvent(event);
};

const handleRequestError = (response) => {
  if (!response.ok) {
    throw Error(response.statusText);
  }

  return response;
};

const handleRequestResponse = (response) => {
  return handleRequestError(response).json();
};

const showErrorNotification = (error) => {
  const isErrorObj = error instanceof Error;
  const message = isErrorObj ? error.message : error;

  showNotification({
    message,
    label: NOTIFICATION_ERROR_LABEL,
  });
};

const mapChildrenToSubitems = (location) => {
  location.totalSubitemsCount = location.totalChildrenCount;
  location.subitems = location.children;

  delete location.totalChildrenCount;
  delete location.children;
  delete location.displayLimit;

  return location;
};

const loadLocationItems = ({ siteaccess }, parentLocationId, callback, limit = 50, offset = 0) => {
  const request = new Request(`${ENDPOINT_LOAD_SUBITEMS}/${parentLocationId}/${limit}/${offset}`, {
    method: 'GET',
    mode: 'same-origin',
    credentials: 'same-origin',
    headers: {
      Accept: 'application/vnd.ez.api.ContentTreeNode+json',
      'X-Siteaccess': siteaccess,
    },
  });

  fetch(request)
      .then(handleRequestResponse)
      .then((data) => {
        const location = data.ContentTreeNode;

        location.children = location.children.map(mapChildrenToSubitems);

        return mapChildrenToSubitems(location);
      })
      .then(callback)
      .catch(showErrorNotification);
};

export default class ContentTreeView extends PureComponent {
  constructor (props) {
    super(props)
    this.tree = null
    this.getTreeData = this.getTreeData.bind(this)
    this.handleCheck = this.handleCheck.bind(this)
  }

  getTreeData (node, callback) {
    const parentLocationId = node.id === '#' ? this.props.treeRootLocationId : node.data.locationId
    if (parentLocationId !== undefined) {
      this.loadMoreSubitems({ parentLocationId, offet: 0, limit: 100 }, (location) => {
        if (node.id === '#') {
          callback.call(this, this.generateNodeFromLocation(location, '#'))
        } else {
          callback.call(this, this.generateNodesFromLocationChildren(location))
        }
      })
    }
  }

  generateNodesFromLocationChildren (location) {
    const children = []
    for (const subitem of location.subitems) {
      children.push(this.generateNodeFromLocation(subitem, location.id))
    }

    return children
  }

  generateNodeFromLocation (location, parentId) {
    const hasChildren = location.totalSubitemsCount > 0
    const children = this.generateNodesFromLocationChildren(location)

    return {
      id: String(
        '_' +
          Math.random()
            .toString(36)
            .substr(2, 9)
      ),
      text: location.name,
      // icon: eZ.helpers.contentType.getContentTypeIconUrl(location.contentTypeIdentifier) || eZ.helpers.contentType.getContentTypeIconUrl('file'),
      state: {
        opened: children.length > 0,
        disabled: false
      },
      type: 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem\\ContentMenuItem',
      data: {
        position: 0,
        url: `content:${location.contentId}`,
        target: null,
        options: [],
        locationId: location.locationId
      },
      li_attr: {
        title: `(location id: ${location.locationId} | content id: ${location.contentId})`
      },
      children: children.length > 0 ? children : hasChildren
    }
  }

  loadMoreSubitems ({ parentLocationId, offset, limit }, successCallback) {
    loadLocationItems(
      this.props.restInfo,
      parentLocationId,
      successCallback,
      limit,
      offset
    )
  }

  handleCheck (operation, node, parentNode, nodePosition, more) {
    if (operation === 'copy_node') { return false }
    return true
  }

  componentDidMount () {
    this.tree = $(this.treeContainer)
      .jstree({
        core: {
          data: this.getTreeData,
          check_callback: this.handleCheck,
          worker: false
        },
        types: this.props.jsTreeTypes,
        dnd: {
          always_copy: true,
          drag_selection: false
        },
        plugins: ['state', 'dnd', 'types']
      })
      .jstree(true)
    $(document)
      .on('dnd_stop.vakata.jstree', this.onDnDStart.bind(this))
  }

  onDnDStart (e, data) {
    if (data.data.origin !== this.tree) {
      return
    }
    const nodes = []
    for (let i = 0, j = data.data.nodes.length; i < j; i++) {
      const node = data.data.origin ? data.data.origin.get_node(data.data.nodes[i]) : data.data.nodes[i]
      const newNode = JSON.parse(JSON.stringify(node))
      newNode.state.loaded = true
      newNode.children = []
      newNode.children_d = []
      newNode.id = String(
        '_' +
          Math.random()
            .toString(36)
            .substr(2, 9)
      )
      nodes[i] = newNode
    }
    data.data.nodes = nodes
    data.data.origin = null
  }

  componentDidUpdate () {
    this.tree.settings.core.data = this.getTreeData
    this.tree.refresh({ skip_loading: true })
  }

  render () {
    return <div ref={(div) => (this.treeContainer = div)} />
  }
}

ContentTreeView.propTypes = {
  jsTreeTypes: PropTypes.object,
  treeRootLocationId: PropTypes.number.isRequired,
  restInfo: PropTypes.shape({
    token: PropTypes.string.isRequired,
    siteaccess: PropTypes.string.isRequired
  }).isRequired
}
