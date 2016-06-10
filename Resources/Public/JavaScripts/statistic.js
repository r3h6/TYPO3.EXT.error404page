(function (factory) {
if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module depending on jQuery.
    // define(['jquery'], factory);
    define(["jquery", "//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"], factory);
} else {
    // No AMD. Register plugin with global jQuery object.
    factory(jQuery);
}
}(function ($) {

    var r3h6 = r3h6 || {};

    r3h6.ErrorGroupedByDayChart = function (el, data)
    {
        var labels = [];
        var points = [];
        for (var i = 0; i < data['errors'].length; i++){
            labels.push(data['errors'][i]['dayDate']);
            points.push(data['errors'][i]['counter']);
        }

        this.chart = Chart.Line(el.getContext('2d'), {
            data: {
                labels: labels,
                datasets: [{
                    label: "Errors",
                    fill: false,
                    lineTension: 0,
                    data: points
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    }

    $(document).ready(function($) {
        $('canvas[data-chart]').each(function(i, el){
            $.get($(this).data('chart'), function(data){
                // try {
                    var className = data['demand']['type'] + 'Chart';
                    console.log(className);
                    (new r3h6[className](el, data));
                // } catch (e){
                //     console.log(e);
                //     // top.TYPO3.Notification.error("", e);
                // }
            });
        })
    });

}));