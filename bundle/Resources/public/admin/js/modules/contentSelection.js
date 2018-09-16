var eZMailingContentSelectionModule = function () {
    function _init($, $app) {
        $("button.js-novaezmailing-select-location-id", $app).click(function () {
            var token = document.querySelector('meta[name="CSRF-Token"]').content;
            var siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
            var udwContainer = $("#react-udw").get(0);
            var startingLocationId = 1;
            var targetSelector = $(this).data('target-id');
            var targetSelectorName = $(this).data('target-name');
            ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
                onCancel: function () {
                    ReactDOM.unmountComponentAtNode(udwContainer)
                },
                multiple: false,
                startingLocationId: startingLocationId,
                restInfo: {token: token, siteaccess: siteaccess},
                onConfirm: function (response) {
                    $(targetSelector).val(response[0].id);
                    $(targetSelectorName).text(response[0].ContentInfo.Content.Name);
                    ReactDOM.unmountComponentAtNode(udwContainer);
                }
            }), udwContainer);
        });
    }

    return {init: _init};
}();
