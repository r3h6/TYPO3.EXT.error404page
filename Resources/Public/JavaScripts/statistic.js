(function (factory) {
if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module depending on jQuery.
    // define(['jquery'], factory);
    define(["jquery", "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"], factory);
} else {
    // No AMD. Register plugin with global jQuery object.
    factory(jQuery);
}
}(function ($) {

    var r3h6 = r3h6 || {};

    r3h6.ErrorGroupedByDayChart = function (el, data)
    {
        this.el = el;
        this.$el = $(el);
        this.data = data;

        labels = [];
        points = [];
        for (var i in data['errors']){
            labels.push(data['errors'][i]['dayDate']);
            points.push(data['errors'][i]['counter']);
        }

        this.chart = Chart.Line(el.getContext('2d'), {
            data: {
                labels: labels,
                datasets: [{
                    label: "Errors",
                    fill: false,
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
                var className = data['demand'] + 'Chart';
                (new r3h6[className](el, data));
            });
        })
    });

}));