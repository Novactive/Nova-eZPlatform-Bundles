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

        $("button.js-novaezmailing-create-content", $app).click(function () {
            var token = document.querySelector('meta[name="CSRF-Token"]').content;
            var siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
            var udwContainer = $("#react-udw").get(0);
            var parentLocationId = $(this).data('parent-location-id');
            var title = $(this).find('span').html();
            ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
                onCancel: function () {
                    ReactDOM.unmountComponentAtNode(udwContainer)
                },
                title: title,
                multiple: false,
                startingLocationId: 1,
                canSelectContent: function (data) {
                    var contentDisabled = true;
                    if (data.item.pathString.split('/').includes(parentLocationId.toString()) && $('.c-choose-content-type__list .is-selected').length > 0) {
                        contentDisabled = false;
                    }
                    $('button.m-ud__action--create-content').prop("disabled", contentDisabled);
                },
                restInfo: {token: token, siteaccess: siteaccess},
                visibleTabs: ['create'],
                activeTab: 'create',
                onConfirm: function (response) {
                    ReactDOM.unmountComponentAtNode(udwContainer);
                    location.reload();
                }
            }), udwContainer);
        });
    }

    return {init: _init};
}();
