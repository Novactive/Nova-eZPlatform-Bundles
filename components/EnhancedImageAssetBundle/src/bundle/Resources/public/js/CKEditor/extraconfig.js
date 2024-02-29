import EnhancedImageEmbedImageEditing from "./embed/image/embed-image-editing";

(function (ibexa) {

    ibexa.addConfig('richText.CKEditor.extraPlugins', [
        EnhancedImageEmbedImageEditing
    ], true);

})(window.ibexa);
