import UniversalDiscoveryModule
    , {
    EditOnTheFlyDataContext, RestInfoContext
} from "@ibexa-admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/universal.discovery.module";
import {
    createDraft
} from "@ibexa-admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/services/universal.discovery.service";
import React, {createContext} from "react";

export const ContentEditContext = createContext();

const ContentEditModule = ({editContext, ...props}) => {

    props['activeTab'] = 'standalone-content-edit';

    return <ContentEditContext.Provider value={editContext}>
        <UniversalDiscoveryModule {...props}/>
    </ContentEditContext.Provider>
}


ContentEditModule.propTypes = UniversalDiscoveryModule.propTypes;

ContentEditModule.defaultProps = UniversalDiscoveryModule.defaultProps;

ibexa.addConfig('modules.ContentEdit', ContentEditModule);
