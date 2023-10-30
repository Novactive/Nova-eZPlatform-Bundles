import ContentEdit from '../content-edit/content-edit'

(function (ibexa) {

    ibexa.addConfig('richText.CKEditor.extraPlugins', [
        ContentEdit
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
