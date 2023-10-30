import Plugin from '@ckeditor/ckeditor5-core/src/plugin';

import ContentEditUI from './content-edit-ui';
import ContentEditEditing from './content-edit-editing';

class ContentEdit extends Plugin {
    static get requires() {
        return [ContentEditUI, ContentEditEditing];
    }
}

export default ContentEdit;
