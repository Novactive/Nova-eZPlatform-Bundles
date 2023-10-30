import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import ClickObserver from '@ckeditor/ckeditor5-engine/src/view/observer/clickobserver';
import IbexaButtonView
    from "../../../../../../../../../ibexa/public/bundles/ibexafieldtyperichtext/js/CKEditor/common/button-view/button-view";


class ContentEditUI extends Plugin {
    constructor(props) {
        super(props);

        this.editContent = this.editContent.bind(this);
    }

    // getSelectedElement() {
    //     return this.editor.model.document.selection.getSelectedElement();
    // }

    editContent() {
        this.editor.execute('contentEdit');
    }

    createButton(label, icon, locale) {
        const buttonView = new IbexaButtonView(locale);
        const command = this.editor.commands.get('contentEdit');

        buttonView.set({
            label: label,
            icon: icon,
            tooltip: true
        });

        buttonView.bind('isOn').to(command, 'value');
        this.listenTo(buttonView, 'execute', this.editContent.bind(this));

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

        this.editor.editing.view.addObserver(ClickObserver);
    }
}


export default ContentEditUI;
