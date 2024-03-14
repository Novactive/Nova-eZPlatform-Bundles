import EmbedEditBaseUi from "../embed-edit-base-ui";
import EmbedImageEditCommand from "./embed-image-edit-command";

class EmbedImageEditUi extends EmbedEditBaseUi {

    constructor(props) {
        super(props);

        this.configName = 'richtext_embed_image';
        this.commandName = 'updateIbexaEmbedImage';
        this.buttonLabel = Translator.trans(/*@Desc("Edit image")*/ 'image_edit.label', {}, 'ck_editor');
        this.componentName = 'embedImageEdit';
        this.icon = ibexa.helpers.icon.getIconPath('edit');
    }

    init() {
        this.editor.commands.add('updateIbexaEmbedImage', new EmbedImageEditCommand(this.editor));
        return super.init();
    }
}

export default EmbedImageEditUi;
