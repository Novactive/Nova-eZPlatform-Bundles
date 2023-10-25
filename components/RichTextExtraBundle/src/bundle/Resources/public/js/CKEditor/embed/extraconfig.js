import ContentEdit from '../contentEdit/ContentEdit'

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
    }, true);


})(window.ibexa);
