// let ctx = $('#booksByCat');
// let myChart = new Chart(ctx, {
//     type: 'doughnut',
//     data: {
//         labels: ['Informatique', 'Economie', 'Gestion', 'Management', 'Communication'],
//         datasets: [{
//             label: '# of Votes',
//             data: [12, 19, 3, 5, 2, 3],
//             backgroundColor: [
//                 'rgba(255, 99, 132, 0.8)',
//                 'rgba(54, 162, 235, 0.8)',
//                 'rgba(255, 206, 86, 0.8)',
//                 'rgba(75, 192, 192, 0.8)',
//                 'rgba(153, 102, 255, 0.8)',
//             ],
//             borderColor: [
//                 'rgba(255, 99, 132, 1)',
//                 'rgba(54, 162, 235, 1)',
//                 'rgba(255, 206, 86, 1)',
//                 'rgba(75, 192, 192, 1)',
//                 'rgba(153, 102, 255, 1)',
//             ],
//             borderWidth: 0.5
//         }]
//     }
// });

var mode, year;

$('input[name=options]').change(function () {

    mode = $('input[name=options]:checked').val();
    $('.mode').text(mode);
    refresh(year);

});

$('#year').change(function () {
    year = $(this).val();
    $('.chart-year').text(year);
    refresh(year);
}).trigger('change');

function refresh(year) {
    $.ajax({
        url: '/admin/getCatStats/' + year,
        success(result) {
            console.log(result, mode);
            let $container = $('#categories');
            $container.text('');
            let $content = '';
            $.each(result.categories, function (key, category) {
                $content += '<div class="col-6 mb-sm-4 col-md-3 col-lg-3 col-xl-2 mb-0">' +
                    '<div class="text-muted">' + key + '</div>' +
                    '<small><strong>' + category[mode].nbr + '  ' + mode + '  (' + category[mode].prc + '%)</strong></small>' +
                    '<div class="progress progress-xs mt-2">\n' +
                    '<div class="progress-bar bg-success" role="progressbar" style="width: ' + category[mode].prc + '%" ' +
                    'aria-valuenow="' + category[mode].prc + '" aria-valuemin="0" aria-valuemax="100"></div>' +
                    '</div>' +
                    '</div>';
            });
            $container.append($content);

        }
    })
}

$(document).ready(function () {
    $('#option1').trigger('change');
});
