import React, {useRef, useContext, useState} from 'react';

import {
    TabsContext,
    ActiveTabContext,
    RestInfoContext,
    SelectedLocationsContext,
    LoadedLocationsMapContext,
    ConfirmContext, CancelContext
} from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/universal.discovery.module';
import {
    findLocationsById,
    findLocationsByParentLocationId
} from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/services/universal.discovery.service';
import deepClone from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/deep.clone.helper';
import {ContentEditContext} from "./content.edit.module";

const { ibexa, Translator, Routing } = window;

const StandaloneContentEditTabModule = () => {
    const restInfo = useContext(RestInfoContext);
    const tabs = useContext(TabsContext);
    const [, setActiveTab] = useContext(ActiveTabContext);
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const editContext = useContext(ContentEditContext);
    const onConfirm = useContext(ConfirmContext);
    const cancelUDW = useContext(CancelContext);
    const iframeRef = useRef();

    const generateIframeUrl = (editContext) => {
        if(typeof editContext.contentId === "undefined"){
            return null;
        }
        return Routing.generate(
            'ibexa.content.on_the_fly.edit',
            {
                contentId: editContext.contentId,
                versionNo: editContext.versionNo,
                languageCode: editContext.languageCode,
            },
            true,
        )
    }

    const [iframeUrl, setIframeUrl] = useState(generateIframeUrl(editContext));

    const publishContent = () => {
        const submitButton = iframeRef.current.contentWindow.document.body.querySelector('[data-action="publish"]');

        if (submitButton) {
            submitButton.click();
        }
    };
    const handleCancelInIframe = () => {
        event.preventDefault();
        cancelUDW();
    };

    const handleIframeLoad = () => {
        const locationId = iframeRef.current?.contentWindow.document.querySelector('meta[name="LocationID"]');
        const iframeBody = iframeRef.current?.contentWindow.document.body;
        const iframeConfirmBtn = iframeBody.querySelector('.ibexa-context-menu .ibexa-btn--confirm');
        const iframeCancelBtn = iframeBody.querySelector('.ibexa-context-menu .ibexa-btn--cancel');
        const iframeCloseBtn = iframeBody.querySelector('.ibexa-anchor-navigation-menu__close');

        if (locationId) {
            findLocationsById({ ...restInfo, id: parseInt(locationId.content, 10) }, (editedItems) => {
                const items = [{ location: editedItems[0] }];

                onConfirm(items);

                return;
            });
        }
        iframeConfirmBtn?.addEventListener('click', publishContent, false);
        iframeCancelBtn?.addEventListener('click', handleCancelInIframe, false);
        iframeCloseBtn?.addEventListener('click', handleCancelInIframe, false);
    };

    if(!iframeUrl){
        return <div/>
    }

    return (
        <div className="c-content-edit">
            <iframe src={iframeUrl} className="c-content-edit__iframe" ref={iframeRef} onLoad={handleIframeLoad} />
        </div>
    );
};

ibexa.addConfig(
    'adminUiConfig.universalDiscoveryWidget.tabs',
    [
        {
            id: 'standalone-content-edit',
            component: StandaloneContentEditTabModule,
            label: Translator.trans(/*@Desc("Content edit")*/ 'content_edit.label', {}, 'universal_discovery_widget'),
            isHiddenOnList: true,
        },
    ],
    true,
);

export default StandaloneContentEditTabModule;
