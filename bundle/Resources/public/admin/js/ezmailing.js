$(function () {
    "use strict";
    $('[data-toggle="popover"]').popover();
    var $app = $(".novaezmailing-app:first");


    $('.novaezmailing-search > input[type="search"]').autocomplete({
        serviceUrl: $app.data('search-endpoint'),
        minChars: 3,
        onSelect: function (suggestion) {
            location.href = suggestion.data;
        }
    });
});
