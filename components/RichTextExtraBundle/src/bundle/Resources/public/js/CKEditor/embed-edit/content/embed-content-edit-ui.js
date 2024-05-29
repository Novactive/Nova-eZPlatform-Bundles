import EmbedEditBaseUi from "../embed-edit-base-ui";

class EmbedContentEditUi extends EmbedEditBaseUi {
    constructor(props) {
        super(props);

        this.configName = 'richtext_embed';
        this.commandName = null;
        this.buttonLabel = Translator.trans(/*@Desc("Content image")*/ 'content_edit.label', {}, 'ck_editor');
        this.componentName = 'embedContentEdit';
        this.icon = ibexa.helpers.icon.getIconPath('edit');
    }
}

export default EmbedContentEditUi;
