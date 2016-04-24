define(["jquery", "./Chart.min.js"], function($) {



    $(document).ready(function($) {

        $('.ct-chart').each(function(){
            var $chart = $(this);
            $.get($chart.data('source'), function(data){
                if ($chart.data('chart') === 'count') errorsPerDayChart(data, $chart);
            }, 'json');
        });




function errorsPerDayChart(errors, $el){

    var data = {
        labels: [],
        datasets: []
    }
    var dataset = {
        data: []
    };


    for (var i in errors){
        var error = errors[i];
        data.labels.push(error.dayDate);
        dataset.data.push(error.counter);
    }

    data.datasets.push(dataset);

    console.log(data);

    // var d = {
    //     labels = [],
    //     datasets = []
    // };
    // var datasets = {};
    // for (var i in data){
    //     var r = data[i];

    // }

    // var data = {
    //     labels: ["January", "February", "March", "April", "May", "June", "July"],
    //     datasets: [
    //         {
    //             label: "My First dataset",
    //             fillColor: "rgba(220,220,220,0.5)",
    //             strokeColor: "rgba(220,220,220,0.8)",
    //             highlightFill: "rgba(220,220,220,0.75)",
    //             highlightStroke: "rgba(220,220,220,1)",
    //             data: [65, 59, 80, 81, 56, 55, 40]
    //         },
    //         {
    //             label: "My Second dataset",
    //             fillColor: "rgba(151,187,205,0.5)",
    //             strokeColor: "rgba(151,187,205,0.8)",
    //             highlightFill: "rgba(151,187,205,0.75)",
    //             highlightStroke: "rgba(151,187,205,1)",
    //             data: [28, 48, 40, 19, 86, 27, 90]
    //         }
    //     ]
    // };
    var options = {};
    var ctx = $('canvas.ct-chart').get(0);

    var myBarChart = new Chart.Bar(ctx, {data: data});



}



//         console.log("Hello");

//         var data = {
//   labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
//   series: [
//     [5, 4, 3, 7, 5, 10, 3, 4, 8, 10, 6, 8],
//     [3, 2, 9, 5, 4, 6, 4, 6, 7, 8, 7, 4]
//   ]
// };

// var options = {
//   seriesBarDistance: 10
// };

// var responsiveOptions = [
//   ['screen and (max-width: 640px)', {
//     seriesBarDistance: 5,
//     axisX: {
//       labelInterpolationFnc: function (value) {
//         return value[0];
//       }
//     }
//   }]
// ];

// new Chartist.Bar('.ct-chart', data, options);



    });
});