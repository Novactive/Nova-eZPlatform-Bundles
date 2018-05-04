var eZMailingEditFormModule = function () {
    function _init($app) {
        var $form = $('form[novaezmailing]', $app);
        var $saveButton = $("#novaezmailing_save-tab", $app);
        var $cancelButton = $("#novaezmailing_cancel-tab", $app);

        $cancelButton.click(function () {
            history.back();
        });
        $saveButton.click(function () {
            $form.submit();
        });

    }

    return {init: _init};
}();

