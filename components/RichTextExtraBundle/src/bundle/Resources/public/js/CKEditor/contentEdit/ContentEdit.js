import Plugin from '@ckeditor/ckeditor5-core/src/plugin';

import ContentEditUI from './contentEditUI';
// import IbexaBlockAlignmentEditing from './block-alignment-editing';

class ContentEdit extends Plugin {
    static get requires() {
        return [ContentEditUI];
    }
}

export default ContentEdit;
