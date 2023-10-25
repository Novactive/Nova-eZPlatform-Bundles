import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import IbexaButtonView
    from "../../../../../../../../../ibexa/public/bundles/ibexafieldtyperichtext/js/CKEditor/common/button-view/button-view";


class ContentEditUI extends Plugin {

    getSelectedElement() {
        return this.editor.model.document.selection.getSelectedElement();
    }

    createButton(label, icon, locale) {
        const buttonView = new IbexaButtonView(locale);
        // const commmand = this.editor.commands.get('openEditModal');

        buttonView.set({
            label: label,
            icon: icon,
            tooltip: true
        });

        return buttonView;
    }

    init() {
        this.editor.ui.componentFactory.add(
            'contentEdit',
            this.createButton.bind(
                this,
                Translator.trans('content_edit', {}, 'ck_editor'),
                ibexa.helpers.icon.getIconPath('edit'),
            ),
        );
    }
}


export default ContentEditUI;
