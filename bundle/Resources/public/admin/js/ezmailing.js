$(function () {
    "use strict";
    $('[data-toggle="popover"]').popover();
    var $app = $(".novaezmailing-app:first");
    eZMailingApprobationModule.init($app);
    eZMailingSearchModule.init($app);
    eZMailingChartsModule.init($app);
    eZMainlingNormalizeModule.init($app);
    eZMailingSubItemsModule.init($app);
    eZMailingEditFormModule.init($app);
    eZMailingContentSelectionModule.init($app);
});
