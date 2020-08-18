/*
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 *
 */

(function (global, doc) {
    const udwContainer = doc.getElementById('react-udw');
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
    const selectLocationsConfirm = (target, data) => {
        const selectedItems = data.reduce((total, item) => total + `<li class="path-location"><div class="pull-left">${item.ContentInfo.Content.Name}</div><a class="btn btn-primary delete-rss-items pull-right"><svg class="ez-icon ez-icon-trash"><use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#trash"></use></svg> </a> </li>`, '');

        doc.querySelector(target.dataset.locationInputSelector).value = data.map(item => item.id).join();
        console.log(selectedItems);
        doc.querySelector(target.dataset.selectedLocationListSelector).innerHTML = selectedItems;

        closeUDW();
    };
    const openUDW = (event) => {
        event.preventDefault();

        ReactDOM.render(React.createElement(global.eZ.modules.UniversalDiscovery, {
            onConfirm: selectLocationsConfirm.bind(this, event.target),
            onCancel: closeUDW,
            confirmLabel: 'Add locations',
            title: 'Choose locations',
            startingLocationId: window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId,
            multiple: true,
            restInfo: {token, siteaccess}
        }), udwContainer);
    };

    /**
     * get tree selector Location Info
     * @param selector
     */
    const getTreeDiscoveryLocationInfo = (selector) => {
        if (selector.dataset) {
            // get selector of locationId
            locationInputSelector = selector.dataset["locationInputSelector"];
            selectedLocationListSelector = selector.dataset["selectedLocationListSelector"];

            // get locationId
            locationId = doc.querySelector(locationInputSelector).value;
            rssLocationUrlAjax = TWIG.loadLocationRssInfo + "/" + locationId;
            const req = new XMLHttpRequest();
            req.open('GET', rssLocationUrlAjax, false);
            req.send(null);
            // get location => id, content => [id, name]
            if (req.status === 200) {
                data = JSON.parse(req.response);
                if (typeof data.content != undefined && typeof data.content.name != undefined) {
                    doc.querySelector(selectedLocationListSelector).innerHTML = `<li class="path-location"><div class="pull-left">${data.content.name}</div><a class="btn btn-primary delete-rss-items pull-right"><svg class="ez-icon ez-icon-trash"><use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#trash"></use></svg> </a> </li>`;
                }

            }
        }
    };


    const rssItemAddFn = (event) => {
        [...doc.querySelectorAll('.ez-pick-subtree-button')].forEach(btn => btn.addEventListener('click', openUDW, false));
    };
    [...doc.querySelectorAll('.ez-pick-subtree-button')].forEach(btn => {
        btn.addEventListener('click', openUDW, false);
        getTreeDiscoveryLocationInfo(btn);
    });
    doc.addEventListener("rss.item.add", rssItemAddFn, false);
})(window, document);
