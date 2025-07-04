fetch('get_top_products.php')
    .then(response => response.json())
    .then(data => {
        let names = data.map(item => item.name);
        let clicks = data.map(item => item.clicks);

        let ctx = document.getElementById('clickChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: names,
                datasets: [{
                    label: 'Cliques por Produto',
                    data: clicks,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    });
