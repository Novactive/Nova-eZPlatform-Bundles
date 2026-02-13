import Plugin from '@ckeditor/ckeditor5-core/src/plugin';

import IbexaButtonView from '@ibexa-richtext/src/bundle/Resources/public/js/CKEditor/common/button-view/button-view';

const { ibexa, Translator } = window;

class UploadFileUi  extends Plugin {
    constructor(props) {
        super(props);

        this.openFileSelector = this.openFileSelector.bind(this);
    }

    createFileSelector() {
        const fileSelector = document.createElement('input');
        const allowedExtensions = ibexa.adminUiConfig.fileUpload.mime_types.join(',')

        fileSelector.setAttribute('type', 'file');
        fileSelector.setAttribute('accept', allowedExtensions);

        return fileSelector;
    }

    openFileSelector() {
        const fileSelector = this.createFileSelector();

        fileSelector.addEventListener(
            'change',
            ({ currentTarget }) => this.editor.execute('insertIbexaUploadFile', { file: currentTarget.files[0] }),
            false,
        );

        fileSelector.click();
    }

    init() {
        this.editor.ui.componentFactory.add('ibexaUploadFile', (locale) => {
            const buttonView = new IbexaButtonView(locale);

            buttonView.set({
                label: Translator.trans(/*@Desc("Upload file")*/ 'upload_file_btn.label', {}, 'ck_editor'),
                icon: ibexa.helpers.icon.getIconPath('upload'),
                tooltip: true,
            });

            this.listenTo(buttonView, 'execute', this.openFileSelector);

            return buttonView;
        });
    }
}

export default UploadFileUi;
