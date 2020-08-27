export const eZMailingSubItemsModule = function () {
    var token, siteaccess;

    function _init($, $app) {
        token = document.querySelector('meta[name="CSRF-Token"]').content;
        siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        $(".ezmailing-subitem-children", $app).each(function ($container) {
            _generate($(this));
        });
    }

    function _generate($container) {
        var subitemsList = $container.data('items').SubitemsList;
        var items = subitemsList.SubitemsRow.map(function (elt) {
            return {
                content: elt.Content,
                location: elt.Location
            }
        });
        var contentTypes = $container.data('contentTypes').ContentTypeInfoList.ContentType;
        var contentTypesMap = contentTypes.reduce(function (total, item) {
            total[item._href] = item;
            return total;
        }, {});

        ReactDOM.render(React.createElement(eZ.modules.SubItems, {
            parentLocationId: $container.data('location'),
            limit: $container.data('limit'),
            items: items,
            contentTypesMap: contentTypesMap,
            totalCount: subitemsList.ChildrenCount,
            handleEditItem: function (content) {
                alert("@todo: please PR to https://github.com/Novactive/Nova-eZPlatform-Bunddles");
            },
            generateLink: function (locationId,contentId) {
                return window.Routing.generate('_ez_content_view', { locationId, contentId });
            },
            restInfo: {token: token, siteaccess: siteaccess}
        }), $container.get(0));
    }

    return {init: _init};
}();
