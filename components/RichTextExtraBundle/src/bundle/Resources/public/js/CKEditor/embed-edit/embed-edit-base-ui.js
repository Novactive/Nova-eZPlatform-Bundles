import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import IbexaButtonView from "@ibexa-richtext/src/bundle/Resources/public/js/CKEditor/common/button-view/button-view.js";
import {createDraft, deleteDraft, getVersions} from "./embed-edit-services";


class EmbedEditBaseUi extends Plugin {
    constructor(props) {
        super(props);

        this.editContent = this.editContent.bind(this);
        this.restInfo = {
            token: document.querySelector('meta[name="CSRF-Token"]').content,
            siteaccess: document.querySelector('meta[name="SiteAccess"]').content,
        };
    }

    getCommandOptions() {}

    openContentEdit = (config) => {
        const udwContainer = document.querySelector('#react-udw');
        const udwRoot = ReactDOM.createRoot(udwContainer);
        const confirmHandler = (items) => {
            if (typeof config.onConfirm === 'function') {
                config.onConfirm(items);
            }

            udwRoot.unmount();
        };
        const cancelHandler = () => {
            if (typeof config.onCancel === 'function') {
                config.onCancel();
            }

            udwRoot.unmount();
        };
        const mergedConfig = { ...config, onConfirm: confirmHandler, onCancel: cancelHandler };

        udwRoot.render(React.createElement(ibexa.modules.ContentEdit, mergedConfig));
    };

    createContentDraft(contentId, callback) {
        createDraft(
            {
                ...this.restInfo,
                contentId,
            },
            (response) =>   {
                const [languageCode] = ibexa.adminUiConfig.languages.priority
                callback({
                    contentId,
                    versionNo: response.Version.VersionInfo.versionNo,
                    languageCode: languageCode,
                });
            }
        );
    }


    deleteContentDraft(contentId, versionNo) {
        deleteDraft({
            ...this.restInfo,
            contentId,
            versionNo
        })
    }

    editContent() {
        const modelElement = this.editor.model.document.selection.getSelectedElement();

        this.createContentDraft(
            modelElement.getAttribute('contentId'),
            (editContext) => {
                const languageCode = document.querySelector('meta[name="LanguageCode"]').content;
                const config = JSON.parse(document.querySelector(`[data-udw-config-name="${this.configName}"]`).dataset.udwConfig);

                const mergedConfig = {
                    onConfirm: this.confirmHandler.bind(this),
                    onCancel: this.cancelHandler.bind(this, editContext),
                    multiple: false,
                    ...config,
                    contentOnTheFly: {
                        allowedLanguages: [languageCode],
                    },
                    editContext: editContext
                };

                this.openContentEdit(mergedConfig);
            }
        )
    }


    confirmHandler(items) {
        this.editor.focus();
        if(this.commandName) {
            this.editor.execute(this.commandName, this.getCommandOptions(items));
        }
    }

    cancelHandler({contentId, versionNo}) {
        this.editor.focus();
        this.deleteContentDraft(contentId, versionNo)
    }


    init() {
        this.editor.ui.componentFactory.add(this.componentName, (locale) => {
            const buttonView = new IbexaButtonView(locale);

            buttonView.set({
                label: this.buttonLabel,
                icon: this.icon,
                tooltip: true,
            });

            this.listenTo(buttonView, 'execute', this.editContent);

            return buttonView;
        });
    }
}


export default EmbedEditBaseUi;
