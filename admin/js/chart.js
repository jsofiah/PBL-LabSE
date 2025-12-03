document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('artikelChart').getContext('2d');

    const chartColors = [
        'rgba(20, 136, 204, 0.8)',
        'rgba(255, 128, 8, 0.8)',
        'rgba(102, 126, 234, 0.8)',
        'rgba(17, 153, 142, 0.8)',
        'rgba(238, 9, 121, 0.8)',
        'rgba(0, 198, 255, 0.8)'
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: artikelData.map(item => item.kategori),
            datasets: [{
                data: artikelData.map(item => item.jumlah),
                backgroundColor: chartColors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            family: 'Poppins'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

});