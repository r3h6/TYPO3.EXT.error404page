define(["jquery", "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"], function($) {

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

});