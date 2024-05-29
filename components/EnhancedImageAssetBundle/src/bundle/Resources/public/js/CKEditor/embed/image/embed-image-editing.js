import IbexaEmbedImageEditing from '@ibexa-richtext/src/bundle/Resources/public/js/CKEditor/embed/image/embed-image-editing.js'
import IbexaEmbedImageCommand from '@ibexa-richtext/src/bundle/Resources/public/js/CKEditor/embed/image/embed-image-command.js'
import { findContent } from '@ibexa-richtext/src/bundle/Resources/public/js/CKEditor/services/content-service.js';
import Widget from '@ckeditor/ckeditor5-widget/src/widget';
class EnhancedImageEmbedImageEditing extends IbexaEmbedImageEditing {
    loadImagePreview(modelElement) {
        const contentId = modelElement.getAttribute('contentId');
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;

        findContent({ token, siteaccess, contentId }, (contents) => {
            const fields = contents[0].CurrentVersion.Version.Fields.field;
            const fieldImage = fields.find((field) => ['enhancedimage', 'ezimage'].includes(field.fieldTypeIdentifier));
            const size = modelElement.getAttribute('size');
            const variationHref = fieldImage.fieldValue.variations[size].href;

            this.loadImageVariation(modelElement, variationHref);
        });
    }

    init() {
        this.defineConverters();

        this.editor.commands.add('insertIbexaEmbedImage', new IbexaEmbedImageCommand(this.editor));
    }
}


export default EnhancedImageEmbedImageEditing;
