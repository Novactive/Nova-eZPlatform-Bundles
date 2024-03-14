import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import Widget from '@ckeditor/ckeditor5-widget/src/widget';
import UploadFileCommand from "./upload-file-command";

class UploadFileEditing extends Plugin {
    static get requires() {
        return [Widget];
    }

    addListeners() {
    }

    init() {
        this.addListeners();

        this.editor.commands.add('insertIbexaUploadFile', new UploadFileCommand(this.editor));
    }
}

export default UploadFileEditing;
