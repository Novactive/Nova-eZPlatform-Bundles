import ContentEdit from '../content-edit/content-edit'
import IbexaEmbedToolbar from "./embed-toolbar";

(function (ibexa) {

    ibexa.addConfig('richText.CKEditor.extraPlugins', [
        ContentEdit,
        IbexaEmbedToolbar
    ], true);

    ibexa.addConfig('richText.CKEditor.extraConfig', {
        embedImage: {
            toolbar: [
                'imageVarations',
                'ibexaBlockLeftAlignment',
                'ibexaBlockCenterAlignment',
                'ibexaBlockRightAlignment',
                'ibexaRemoveElement',
                'contentEdit',
            ],
        },
        embed: {
            toolbar: [
                'contentEdit',
            ],
        }
    }, true);


})(window.ibexa);
