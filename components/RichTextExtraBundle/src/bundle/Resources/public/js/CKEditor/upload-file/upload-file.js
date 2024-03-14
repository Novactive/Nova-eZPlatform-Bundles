import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import UploadFileEditing from "./upload-file-editing";
import UploadFileUi from "./upload-file-ui";

class UploadFile extends Plugin {

    static get requires() {
        return [UploadFileEditing, UploadFileUi]
    }
}

export default UploadFile;
