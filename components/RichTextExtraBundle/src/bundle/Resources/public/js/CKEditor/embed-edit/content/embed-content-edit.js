import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import EmbedContentEditUi from "./embed-content-edit-ui";

class EmbedContentEdit extends Plugin {

    static get requires() {
        return [EmbedContentEditUi];
    }
}

export default EmbedContentEdit;
