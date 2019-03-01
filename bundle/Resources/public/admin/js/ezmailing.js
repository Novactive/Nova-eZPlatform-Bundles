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

    $('.campaigns > ul > li > label').click(function () {
        window.location = $(this).parent().find('ul > li.subscriptions > a').attr('href');
        return false;
    });

    $('.campaigns > ul > li > input').click(function () {
        if ($(this).prop('checked')) {
            $(this).parent().addClass('expand');
        } else {
            $(this).parent().removeClass('expand');
        }
    });
});
