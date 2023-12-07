import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import Widget from '@ckeditor/ckeditor5-widget/src/widget';
import UploadFileCommand from "./upload-file-command";

class UploadFileEditing extends Plugin {
    static get requires() {
        return [Widget];
    }

    addListeners() {
        this.listenTo(this.editor.editing.view.document, 'drop', (event, data) => {
            if (data.dataTransfer.effectAllowed === 'copyMove') {
                return;
            }

            const { files } = data.dataTransfer;

            if (!files.length) {
                return;
            }

            this.editor.model.change((writer) => {
                writer.setSelection(this.editor.editing.mapper.toModelRange(data.dropRange));
            });

            files.forEach((file) => {
                if (file.type.includes('image')) {
                    this.editor.execute('insertIbexaUploadFile', { file });
                }
            });
        });
    }

    init() {
        this.addListeners();

        this.editor.commands.add('insertIbexaUploadFile', new UploadFileCommand(this.editor));
    }
}

export default UploadFileEditing;
