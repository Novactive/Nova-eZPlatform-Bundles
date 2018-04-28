$(function () {
    "use strict";
    $('[data-toggle="popover"]').popover();
    var $app = $(".novaezmailing-app:first");
    eZMailingApprobationModule.init($app);
    eZMailingSearchModule.init($app);
    eZMailingChartsModule.init($app);
    eZMainlingNormalizeModule.init($app);
    eZMailingSubItemsModule.init($app);

    // to externalize when finish
    $("#new-mailing", $app).click(function () {
        var token = document.querySelector('meta[name="CSRF-Token"]').content;
        var siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        var udwContainer = $("#react-udw").get(0);
        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
            confirmLabel: 'CONFIRM LABEL',
            title: 'TITLE',
            onCancel: function () {
                ReactDOM.unmountComponentAtNode(udwContainer)
            },
            multiple: false,
            startingLocationId: 1,
            restInfo: {token: token, siteaccess: siteaccess},
            onConfirm: function (response) {
                console.log(response[0].id);
                alert(response[0].id);
                ReactDOM.unmountComponentAtNode(udwContainer);
            }
        }), udwContainer);
    });



});
