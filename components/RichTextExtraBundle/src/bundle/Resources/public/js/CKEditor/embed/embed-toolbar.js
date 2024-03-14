import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import WidgetToolbarRepository from '@ckeditor/ckeditor5-widget/src/widgettoolbarrepository';

class EmbedToolbar extends Plugin {
    static get requires() {
        return [WidgetToolbarRepository];
    }

    getSelectedEmbedWidget(selection) {
        const viewElement = selection.getSelectedElement();
        const isEmbed = viewElement?.hasClass('ibexa-embed');

        return isEmbed ? viewElement : null;
    }

    afterInit() {
        const { editor } = this;
        const widgetToolbarRepository = editor.plugins.get(WidgetToolbarRepository);

        widgetToolbarRepository.register('embed', {
            ariaLabel: editor.t('Embed toolbar'),
            items: editor.config.get('embed.toolbar') || [],
            getRelatedElement: this.getSelectedEmbedWidget,
        });
    }
}

export default EmbedToolbar;
