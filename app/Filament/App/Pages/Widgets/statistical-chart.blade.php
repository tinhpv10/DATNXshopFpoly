<div>
    <h2>Danh sách cần làm:</h2>
    <h3>{{ $this->heading }}</h3> <!-- Tên chart nằm riêng -->

    <div>
        <canvas id="chart"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($this->getData());

        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: '{{ $this->getType() }}',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets,
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    });
</script>
