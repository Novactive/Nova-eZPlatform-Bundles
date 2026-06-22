import UploadFile from './upload-file/upload-file';

(function (ibexa) {
    ibexa.addConfig('richText.CKEditor.extraPlugins', [
        UploadFile,
    ], true);
})(window.ibexa);
