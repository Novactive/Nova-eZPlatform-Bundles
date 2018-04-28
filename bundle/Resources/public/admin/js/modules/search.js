var eZMailingSearchModule = function () {
    function _init($app) {
        var $searchNovaeZMailing = $('.novaezmailing-search > input[type="search"]');
        if ($searchNovaeZMailing.length > 0) {
            $searchNovaeZMailing.autocomplete({
                serviceUrl: $app.data('search-endpoint'),
                minChars: 3,
                onSelect: function (suggestion) {
                    location.href = suggestion.data;
                }
            });
        }
    }

    return {init: _init};
}();
