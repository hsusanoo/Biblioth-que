let ctx = $('#main-chart');

var mode, year;

$('input[name=options]').change(function () {

    mode = $('input[name=options]:checked').val();
    $('.mode').text(mode);
    refreshCats(year);

});

$('#option1').trigger('change');

$('#year').change(function () {
    year = $(this).val();
    $('.chart-year').text(year);
    refresh(year);
}).trigger('change');

function refresh(year) {

    $.ajax({
        url: '/admin/getBooksStats/' + year,
        success(result) {
            // Line Chart
            let data = {
                labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                datasets: [
                    {
                        label: 'Livres',
                        data: result.books,
                        backgroundColor: 'rgba(41,87,120,0.35)',
                    },
                    {
                        label: 'Exemplaires',
                        data: result.samples,
                        backgroundColor: 'rgba(80,85,170,0.35)',
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

        }
    });
    refreshCats(year);
}

function refreshCats(year) {
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
            $('.total').text(result['total'+mode]);
        }
    });
}