import React, { useRef, useContext } from 'react';

import {
    TabsContext,
    ActiveTabContext,
    RestInfoContext,
    SelectedLocationsContext,
    LoadedLocationsMapContext,
    EditOnTheFlyDataContext,
} from '../../../../../../../../../ibexa/vendor/ibexa/admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/universal.discovery.module';
import { findLocationsByParentLocationId } from '../../../../../../../../../ibexa/vendor/ibexa/admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/services/universal.discovery.service';
import deepClone from '../../../../../../../../../ibexa/vendor/ibexa/admin-ui/src/bundle/ui-dev/src/modules/common/helpers/deep.clone.helper';

const { ibexa, Translator, Routing } = window;

const RichtextContentEditTabModule = () => {
    const restInfo = useContext(RestInfoContext);
    const tabs = useContext(TabsContext);
    const [, setActiveTab] = useContext(ActiveTabContext);
    const [loadedLocationsMap, dispatchLoadedLocationsAction] = useContext(LoadedLocationsMapContext);
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const [editOnTheFlyData, setEditOnTheFlyData] = useContext(EditOnTheFlyDataContext);
    const iframeRef = useRef();
    const publishContent = () => {
        const submitButton = iframeRef.current.contentWindow.document.body.querySelector('[data-action="publish"]');

        if (submitButton) {
            submitButton.click();
        }
    };
    const cancelContentEdit = () => {
        setActiveTab(tabs[0].id);
        setEditOnTheFlyData({});
    };
    const handleContentPublished = (locationId) => {
        const clonedLocationsMap = deepClone(loadedLocationsMap);
        let isInSubitems = false;

        findLocationsByParentLocationId({ ...restInfo, parentLocationId: locationId }, (response) => {
            const clonedSelectedLocation = deepClone(selectedLocations);
            const index = clonedSelectedLocation.findIndex((clonedLocation) => clonedLocation.location.id === locationId);

            if (index !== -1) {
                clonedSelectedLocation[index].location = response.location;

                dispatchSelectedLocationsAction({ type: 'REPLACE_SELECTED_LOCATIONS', locations: clonedSelectedLocation });
            }

            dispatchLoadedLocationsAction({ type: 'UPDATE_LOCATIONS', data: response });
        });

        clonedLocationsMap.forEach((clonedLocation) => {
            const subitem = clonedLocation.subitems.find(({ location }) => {
                return location.id === locationId;
            });

            if (subitem) {
                clonedLocation.subitems = [];
                isInSubitems = true;
            }
        });

        if (isInSubitems) {
            dispatchLoadedLocationsAction({ type: 'SET_LOCATIONS', data: clonedLocationsMap });
        }

        cancelContentEdit();
    };
    const handleIframeLoad = () => {
        const locationId = iframeRef.current?.contentWindow.document.querySelector('meta[name="LocationID"]');
        const iframeBody = iframeRef.current?.contentWindow.document.body;
        const iframeConfirmBtn = iframeBody.querySelector('.ibexa-context-menu .ibexa-btn--confirm');
        const iframeCancelBtn = iframeBody.querySelector('.ibexa-context-menu .ibexa-btn--cancel');
        const iframeCloseBtn = iframeBody.querySelector('.ibexa-anchor-navigation-menu__close');

        if (locationId) {
            handleContentPublished(parseInt(locationId.content, 10));
        }

        iframeConfirmBtn?.addEventListener('click', publishContent, false);
        iframeCancelBtn?.addEventListener('click', cancelContentEdit, false);
        iframeCloseBtn?.addEventListener('click', cancelContentEdit, false);
    };
    const iframeUrl = Routing.generate(
        'ibexa.content.on_the_fly.edit',
        {
            contentId: editOnTheFlyData.contentId,
            versionNo: editOnTheFlyData.versionNo,
            languageCode: editOnTheFlyData.languageCode,
            locationId: editOnTheFlyData.locationId,
        },
        true,
    );

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
            id: 'richtext-content-edit',
            component: RichtextContentEditTabModule,
            label: Translator.trans(/*@Desc("Content edit")*/ 'content_edit.label', {}, 'universal_discovery_widget'),
            isHiddenOnList: true,
        },
    ],
    true,
);

export default RichtextContentEditTabModule;
