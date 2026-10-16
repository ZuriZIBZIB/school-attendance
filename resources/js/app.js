import './bootstrap';

const chartEl = document.getElementById('attendance-chart');

if (chartEl) {
    import('chart.js/auto').then(({ default: Chart }) => {
        const data = JSON.parse(chartEl.dataset.chart);

        new Chart(chartEl, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: data.datasets.map((set) => ({
                    label: set.label,
                    data: set.data,
                    backgroundColor: set.color,
                })),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } },
                },
                plugins: { legend: { position: 'bottom' } },
            },
        });
    });
}