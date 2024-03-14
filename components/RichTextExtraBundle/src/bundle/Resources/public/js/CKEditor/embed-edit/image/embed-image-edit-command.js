import Command from '@ckeditor/ckeditor5-core/src/command';
import {findContent} from '@ibexa-richtext/src/bundle/Resources/public/js/CKEditor/services/content-service'
class EmbedImageEditCommand extends Command {
    execute() {
        const modelElement = this.editor.model.document.selection.getSelectedElement();
        this.loadImagePreview(modelElement)
    }

    loadImagePreview(modelElement) {
        const contentId = modelElement.getAttribute('contentId');
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;

        findContent({ token, siteaccess, contentId }, (contents) => {
            const fields = contents[0].CurrentVersion.Version.Fields.field;
            const fieldImage = fields.find((field) => field.fieldTypeIdentifier === 'ezimage');
            const size = modelElement.getAttribute('size');
            const variationHref = fieldImage.fieldValue.variations[size].href;

            this.loadImageVariation(modelElement, variationHref);
        });
    }

    loadImageVariation(modelElement, variationHref) {
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        const request = new Request(variationHref, {
            method: 'GET',
            headers: {
                Accept: 'application/vnd.ibexa.api.ContentImageVariation+json',
                'X-Siteaccess': siteaccess,
                'X-CSRF-Token': token,
            },
            credentials: 'same-origin',
            mode: 'same-origin',
        });

        fetch(request)
            .then((response) => response.json())
            .then((imageData) => {
                this.editor.model.change((writer) => {
                    writer.setAttribute('previewUrl', imageData.ContentImageVariation.uri, modelElement);
                });
            })
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    }
}

export default EmbedImageEditCommand;
