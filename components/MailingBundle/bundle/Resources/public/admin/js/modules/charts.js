var eZMailingChartsModule = function () {

    function _render($chart) {
        var ctx = $chart.get(0);
        var chart = new Chart(ctx, {
            type: $chart.data('type'),
            data: $chart.data('data'),
            options: $chart.data('options')
        });
    }

    function _init($, $app) {
        $app.find("canvas.nova-ezmailing-chart").each(function () {
            _render($(this));
        });
    }

    return {init: _init};
}();
