(function (global, doc, ibexa) {
    ibexa.addConfig('richText.CKEditor.extraConfig', {
        embedImage: {
            toolbar: [
                'imageVarations',
                // 'ibexaBlockLeftAlignment',
                // 'ibexaBlockCenterAlignment',
                // 'ibexaBlockRightAlignment',
                // 'ibexaRemoveElement',
            ],
        }
    }, true);
})(window, window.document, window.ibexa);
