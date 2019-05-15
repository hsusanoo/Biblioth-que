// admin chart
let actx = $('#admin_chart');

$.ajax({
    url: window.location.pathname + '/overview',
    success(result) {
        console.log(result);
        let data = {
            datasets: [{
                label: 'livres',
                data: result.data,
                backgroundColor: [
                    '#2f1f1f',
                    '#644d83',
                    '#a388e2',
                    '#295778',
                    '#00a123',
                    '#42715d',
                    '#455dd5',
                    '#6fbdeb',
                    '#af91f5',
                    '#cf8920',
                    '#f4fa6b',
                    '#b2f756',
                    '#bef693',
                    '#e7f2c0',
                    '#00d1b1',
                    '#6a914a',
                    '#a37e56',
                    '#d46771',
                    '#ec3ce4',
                ]
            }],
            labels: result.category
        };
        let options = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: true,
                position: 'right',
            },
            title: {
                display: true,
                text: 'Statistique des livres ajoutés'
            }
        };

        let adminChart = new Chart(actx, {
            type: 'doughnut',
            data: data,
            options: options
        });
    }
});

