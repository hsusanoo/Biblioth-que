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
                    '#b2f756',
                    '#644d83',
                    '#6fbdeb',
                    '#d46771',
                    '#42715d',
                    '#00d1b1',
                    '#bef693',
                    '#295778',
                    '#af91f5',
                    '#2f1f1f',
                    '#f4fa6b',
                    '#6a914a',
                    '#a388e2',
                    '#a37e56',
                    '#ec3ce4',
                    '#00a123',
                    '#e7f2c0',
                    '#cf8920',
                    '#455dd5',
                ]
            }],
            labels: result.category
        };
        let options = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: false,
                position: 'bottom',
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

