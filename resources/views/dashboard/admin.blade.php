@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row mb-4">
    <!-- Omset Hari Ini -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Omset Hari Ini</p>
                        <h3 class="mb-0">Rp {{ number_format($omsetToday, 0, ',', '.') }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Omset Bulan Ini -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Omset Bulan Ini</p>
                        <h3 class="mb-0">Rp {{ number_format($omsetMonth, 0, ',', '.') }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaksi Hari Ini -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Transaksi Hari Ini</p>
                        <h3 class="mb-0">{{ $transaksiToday }}</h3>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Booking Hari Ini -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Booking Hari Ini</p>
                        <h3 class="mb-0">{{ $bookingsToday->count() }}</h3>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Grafik Omset -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Grafik Omset 7 Hari Terakhir
            </div>
            <div class="card-body">
                <canvas id="omsetChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Lapangan Populer -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-trophy me-2"></i>Lapangan Terpopuler
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($popularLapangan as $item)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $item->lapangan->nama }}
                        <span class="badge bg-primary rounded-pill">{{ $item->total }} booking</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Booking Hari Ini -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar me-2"></i>Jadwal Booking Hari Ini
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Lapangan</th>
                                <th>Customer</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookingsToday as $booking)
                            <tr>
                                <td>{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}</td>
                                <td>{{ $booking->lapangan->nama }}</td>
                                <td>{{ $booking->customer->nama }}</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada booking hari ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stok Produk Menipis -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle me-2"></i>Stok Menipis
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($lowStockProducts as $product)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $product->nama }}
                        <span class="badge bg-danger">{{ $product->stok }}</span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">Semua stok aman</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('omsetChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($omsetChart, 'date')) !!},
            datasets: [{
                label: 'Omset (Rp)',
                data: {!! json_encode(array_column($omsetChart, 'total')) !!},
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endpush