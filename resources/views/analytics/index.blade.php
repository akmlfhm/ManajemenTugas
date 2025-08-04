@extends('layouts.app')

@section('title', 'Analytics')
@section('header', 'Analytics Dashboard')

@section('content')
{{-- Memuat library Chart.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Grafik: Tugas Selesai per Hari -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tugas Selesai (7 Hari Terakhir)</h3>
        <canvas id="tasksCompletedChart"></canvas>
    </div>

    <!-- Grafik: Distribusi Status Tugas -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Distribusi Status Tugas</h3>
        <div class="max-w-xs mx-auto">
            <canvas id="taskStatusChart"></canvas>
        </div>
    </div>

    <!-- Grafik: Kinerja Pengguna -->
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Pengguna (Berdasarkan Tugas Selesai)</h3>
        <canvas id="userPerformanceChart"></canvas>
    </div>
</div>

<script>
    // Menjalankan skrip setelah halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        // Mengambil data yang dikirim dari controller dengan sintaks yang lebih aman untuk linter
        const completionLabels = {!! json_encode($completionLabels) !!};
        const completionData = {!! json_encode($completionData) !!};
        const statusLabels = {!! json_encode($statusLabels) !!};
        const statusData = {!! json_encode($statusData) !!};
        const userLabels = {!! json_encode($userLabels) !!};
        const userData = {!! json_encode($userData) !!};

        // Grafik 1: Tugas Selesai per Hari (Line Chart)
        const ctx1 = document.getElementById('tasksCompletedChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: completionLabels,
                datasets: [{
                    label: 'Tugas Selesai',
                    data: completionData,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Grafik 2: Distribusi Status Tugas (Doughnut Chart)
        const ctx2 = document.getElementById('taskStatusChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.7)', // Pending
                        'rgba(54, 162, 235, 0.7)',  // In Progress
                        'rgba(75, 192, 192, 0.7)'   // Completed
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: { responsive: true }
        });

        // Grafik 3: Kinerja Pengguna (Bar Chart)
        const ctx3 = document.getElementById('userPerformanceChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: userLabels,
                datasets: [{
                    label: 'Jumlah Tugas Selesai',
                    data: userData,
                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    });
</script>
@endsection
