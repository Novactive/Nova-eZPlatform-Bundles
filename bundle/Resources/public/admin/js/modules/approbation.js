var eZMailingApprobationModule = function () {
    function _init($, $app) {
        $(".novaezmailing-registration-approbation button", $app).click(function () {
                var $button = $(this);
                var $container = $button.parents('.novaezmailing-registration-approbation:first');
                var denyEndpoint = $container.data('endpoint-deny');
                var acceptEndpoint = $container.data('endpoint-accept');
                var token = $container.data('token');
                var approveText = $container.data('approve-text');
                var denyText = $container.data('deny-text');
                var action = $button.hasClass("btn-danger") ? "deny" : "approve";
                $.ajax({
                    url: action === 'deny' ? denyEndpoint : acceptEndpoint,
                    method: "POST",
                    data: {token: token},
                    dataType: "json",
                    success: function (data) {
                        $container.attr('data-token', data.token);
                        $button
                            .text(action === 'deny' ? approveText : denyText)
                            .removeClass(action === 'deny' ? 'btn-danger' : 'btn-success')
                            .addClass(action === 'approve' ? 'btn-danger' : 'btn-success')
                        ;
                    },
                    beforeSend: function () {
                        $button.text("...").attr("disabled", "disabled");
                    },
                    complete: function () {
                        $button.removeAttr('disabled');
                    }
                });
            }
        );
    }

    return {init: _init};
}();
