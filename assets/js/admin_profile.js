// admin chart
let actx = $('#admin_chart');

$.ajax({
    url: window.location.pathname + '/overview',
    success(result) {
        let data = {
            labels: result.category,
            datasets: [{
                label: 'livres',
                data: result.data,
                backgroundColor: 'rgba(70,87,200,0.1)',
                pointBorderColor: '#ffffff',
            }]
        };
        let options = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: false,
                position: 'right',
            },
            title: {
                display: true,
                text: 'Statistique des livres ajoutés'
            }
        };

        let adminChart = new Chart(actx, {
            type: 'radar',
            data: data,
            options: options
        });
    }
});

