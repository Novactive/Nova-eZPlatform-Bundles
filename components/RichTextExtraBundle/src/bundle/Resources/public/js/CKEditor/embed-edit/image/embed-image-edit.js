import Plugin from '@ckeditor/ckeditor5-core/src/plugin';

import EmbedImageEditUi from "./embed-image-edit-ui";

class EmbedImageEdit extends Plugin {
    static get requires() {
        return [EmbedImageEditUi];
    }
}

export default EmbedImageEdit;
