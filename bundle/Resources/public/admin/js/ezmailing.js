jQuery(function () {
    "use strict";
    var $ = jQuery;
    $('[data-toggle="popover"]').popover();
    var $app = $(".novaezmailing-app:first");
    eZMailingApprobationModule.init(jQuery, $app);
    eZMailingSearchModule.init(jQuery, $app);
    eZMailingChartsModule.init(jQuery, $app);
    eZMainlingNormalizeModule.init(jQuery, $app);
    eZMailingSubItemsModule.init(jQuery, $app);
    eZMailingEditFormModule.init(jQuery, $app);
    eZMailingContentSelectionModule.init(jQuery, $app);
});
