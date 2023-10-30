import Plugin from '@ckeditor/ckeditor5-core/src/plugin';

import ContentEditCommand from './content-edit-command';


class ContentEditEditing extends Plugin {
    init() {
        this.editor.commands.add('contentEdit', new ContentEditCommand(this.editor));
    }
}

export default ContentEditEditing
