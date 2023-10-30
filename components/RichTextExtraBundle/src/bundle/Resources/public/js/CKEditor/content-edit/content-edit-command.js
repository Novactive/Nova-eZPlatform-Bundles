import Command from '@ckeditor/ckeditor5-core/src/command';

class ContentEditCommand extends Command {
    refresh() {
        this.isEnabled = true;
    }

    async execute() {
        const modelElement = this.editor.model.document.selection.getSelectedElement();
        const container = document.querySelector('#react-udw');

        const config = JSON.parse(
            document.querySelector('div[data-udw-config-name="richtext_embed_image"]').getAttribute('data-udw-config')
        );

        const content = await this.getContentFromRestApiWithContentId(
            modelElement.getAttribute('contentId'),
            document
        );

        const location_id = this.getLastPathId(content['LocationList']['Location'][0]._href);

        config['startingLocationId'] = location_id;
        config['selectedLocations'] = [location_id];

        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
            onConfirm: () => ReactDOM.unmountComponentAtNode(container),
            onCancel: () => ReactDOM.unmountComponentAtNode(container),
            ...config
        }), container);

    }


    async getContentFromRestApiWithContentId(contentId, doc) {
        const token = doc.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
        const request = new Request(`/api/ibexa/v2/content/objects/${contentId}/locations`, {
            method: 'GET',
            mode: 'same-origin',
            credentials: 'same-origin',
            headers: {
                Accept: "application/vnd.ibexa.api.LocationList+json",
                'X-Csrf-Token': token,
                'X-Siteaccess': siteaccess
            }
        });
        const response = await fetch(request);
        return await response.json();
    }

    getLastPathId(path) {
        const splitted_path = path.split('/');

        return splitted_path[splitted_path.length - 1];
    }
}

export default ContentEditCommand;
