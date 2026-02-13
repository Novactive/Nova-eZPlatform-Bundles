import {
    handleRequestResponse
} from "@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/request.helper";
import {
    showErrorNotification
} from "@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/services/notification.service";

const showErrorNotificationAbortWrapper = (error) => {
    if (error?.name === 'AbortError') {
        return;
    }

    return showErrorNotification(error);
};

export const createDraft = ({ token, siteaccess, contentId }, callback) => {
    const request = new Request(`/api/ibexa/v2/content/objects/${contentId}/currentversion`, {
        method: 'COPY',
        headers: {
            Accept: 'application/vnd.ibexa.api.VersionUpdate+json',
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(showErrorNotificationAbortWrapper);
};

export const deleteDraft = ({ token, siteaccess, contentId, versionNo }, callback) => {
    const request = new Request(`/api/ibexa/v2/content/objects/${contentId}/versions/${versionNo}`, {
        method: 'DELETE',
        headers: {
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    fetch(request)
};
