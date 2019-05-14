let ctx = $('#main-chart');

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
            let $container = $('#categories');
            $container.text('');
            let $content = '';
            $.each(result.categories, function (key, category) {
                $content += '<div class="col-6 mb-sm-4 col-md-3 col-lg-3 col-xl-2 mb-0 ' + ((category[mode].nbr === 0) ? 'text-muted' : '') + '">' +
                    '<div>' + key + '</div>' +
                    '<small><strong>' + category[mode].nbr + '  ' + mode + '  (' + category[mode].prc + '%)</strong></small>' +
                    '<div class="progress progress-xs mt-2">\n' +
                    '<div class="progress-bar bg-danger" role="progressbar" style="width: ' + category[mode].prc + '%" ' +
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

    // Line Chart
    let data = {
        labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        datasets: [
            {
                label: mode,
                data: [10, 14, 17, 43, 12, 7, 19, 34, 43, 29, 17, 66],
            }
        ]
    };
    let options = {
        maintainAspectRatio: false,
        responsive: true
    };
    let myChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });

});
