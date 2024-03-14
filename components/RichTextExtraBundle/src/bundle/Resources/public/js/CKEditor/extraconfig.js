import EmbedToolbar from "./embed/embed-toolbar";
import EmbedImageEdit from "./embed-edit/image/embed-image-edit";
import EmbedContentEdit from "./embed-edit/content/embed-content-edit";
import UploadFile from "./upload-file/upload-file";

(function (ibexa) {

    ibexa.addConfig('richText.CKEditor.extraPlugins', [
        EmbedImageEdit,
        EmbedContentEdit,
        EmbedToolbar,
        UploadFile
    ], true);

    ibexa.addConfig('richText.CKEditor.extraConfig', {
        embedImage: {
            toolbar: [
                'imageVarations',
                'ibexaBlockLeftAlignment',
                'ibexaBlockCenterAlignment',
                'ibexaBlockRightAlignment',
                'ibexaRemoveElement',
                'embedImageEdit',
            ],
        },
        embed: {
            toolbar: [
                'embedContentEdit',
            ],
        }
    }, true);


})(window.ibexa);
