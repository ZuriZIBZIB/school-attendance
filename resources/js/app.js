import './bootstrap';

// Cegah klik ganda: tombol submit dinonaktifkan saat form dikirim
document.addEventListener('submit', (event) => {
    const form = event.target;

    if (event.defaultPrevented || form.method.toLowerCase() === 'get') {
        return;
    }

    form.querySelectorAll('button:not([type=button])').forEach((button) => {
        button.disabled = true;
        button.classList.add('opacity-60', 'cursor-wait');
    });
});

// Halaman dari cache tombol Back: muat ulang agar tombol tidak tetap nonaktif
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});

const chartEl = document.getElementById('attendance-chart');

if (chartEl) {
    const loadingEl = document.getElementById('chart-loading');

    import('chart.js/auto')
        .then(({ default: Chart }) => {
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

            loadingEl?.remove();
        })
        .catch(() => {
            if (loadingEl) {
                loadingEl.textContent = 'Grafik gagal dimuat.';
            }
        });
}