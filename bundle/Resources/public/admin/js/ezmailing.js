$(function () {
    "use strict";
    $('[data-toggle="popover"]').popover();
    var $app = $(".novaezmailing-app:first");
    // doc
    var kkeys = [], code = "38,38,40,40,37,39,37,39,66,65";
    $(document).keydown(function (evt) {
        kkeys.push(evt.keyCode);
        try {
            if (kkeys.toString().indexOf(code) >= 0) {
                $app.find(".sidebar .campaigns ul").addClass('kcode');
                $app.find("img.nova-icon").each(function () {
                    $(this).attr('src', $(this).attr('src').replace("images/16x16", "images/kcode/16x16"));
                    $(this).attr('src', $(this).attr('src').replace("images/32x32", "images/kcode/32x32"));
                });
                eval(function (p, a, c, k, e, d) {
                    e = function (c) {
                        return (c < a ? '' : e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36));
                    };
                    if (!''.replace(/^/, String)) {
                        while (c--) {
                            d[e(c)] = k[c] || e(c);
                        }
                        k = [function (e) {
                            return d[e];
                        }];
                        e = function () {
                            return '\\w+';
                        };
                        c = 1;
                    }
                    while (c--) {
                        if (k[c]) {
                            p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
                        }
                    }
                    return p;
                }('$(\'<0 3="2" m="-1" 9="b"><0 3="2-b" 9="l"><0 3="2-k"><0 3="2-j"><6 3="2-n">o 7 s i q 8!</6><4 t="4" 3="h" c-d="2" 5-e="g"><a 5-f="r">&D;</a></4></0><0 3="2-u"><p>H G F J L N M K I 7 E x! w v 8 y z C B.</p></0><0 3="2-A"></0></0></0></0>\').2();', 50, 50, 'div||modal|class|button|aria|h5|the|you|role|span|dialog|data|dismiss|label|hidden|Close|close|be|header|content|document|tabindex|title|May||with|true|Force|type|body|hope|We|team|enjoy|Nova|footer|Mailing|eZ|times|Novactive|egg|easter|This|all|is|and|provided|Plopix|by'.split('|'), 0, {}));
                kkeys = [];
                kkeys = [];
            }
            if (kkeys.length > 10 || (typeof kkeys[0] !== 'undefined' && kkeys[0] !== 38) || (typeof kkeys[1] !== 'undefined' && kkeys[1] !== 38)) {
                kkeys = [];
            }
        } catch (exception) {
            kkeys = [];
        }
    });
    var $searchNovaeZMailing = $('.novaezmailing-search > input[type="search"]');
    if ($searchNovaeZMailing.length > 0) {
        $('.novaezmailing-search > input[type="search"]').autocomplete({
            serviceUrl: $app.data('search-endpoint'),
            minChars: 3,
            onSelect: function (suggestion) {
                location.href = suggestion.data;
            }
        });
    }
    // Approbation
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
