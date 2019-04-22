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

});

$('#year').change(function () {
    year = $(this).val();
    $('.chart-year').text(year);
}).trigger('change');

$(document).ready(function () {
   $('#option1').trigger('change');
});
