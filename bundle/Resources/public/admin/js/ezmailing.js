$(function () {
    "use strict";
    $('[data-toggle="popover"]').popover();
    var $app = $(".novaezmailing-app:first");
    var kkeys = [], code = "38,38,40,40,37,39,37,39,66,65";
    $(document).keydown(function (e) {
        kkeys.push(e.keyCode);
        if (kkeys.toString().indexOf(code) >= 0) {
            $app.find(".sidebar .campaigns ul").addClass('kcode');
            kkeys = [];
        }
    });
    $('.novaezmailing-search > input[type="search"]').autocomplete({
        serviceUrl: $app.data('search-endpoint'),
        minChars: 3,
        onSelect: function (suggestion) {
            location.href = suggestion.data;
        }
    });
});
