{{-- resources/views/dashboard/kasir.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="row mb-4">
    <!-- Quick Stats -->
    <div class="col-md-6 mb-3">
        <div class="card border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Transaksi Saya Hari Ini</p>
                        <h2 class="mb-0 fw-bold text-primary">{{ $myTransactionsToday }}</h2>
                        <small class="text-muted">{{ \Carbon\Carbon::now()->format('d F Y') }}</small>
                    </div>
                    <div class="text-primary" style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Booking Hari Ini</p>
                        <h2 class="mb-0 fw-bold text-success">{{ $bookingsToday->count() }}</h2>
                        <small class="text-muted">Semua lapangan</small>
                    </div>
                    <div class="text-success" style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt me-2"></i>Aksi Cepat
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('transactions.create') }}" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                            <strong>Transaksi Baru</strong>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('bookings.index') }}" class="btn btn-info w-100 py-3">
                            <i class="fas fa-calendar-alt fa-2x d-block mb-2"></i>
                            <strong>Lihat Jadwal</strong>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('customers.index') }}" class="btn btn-success w-100 py-3">
                            <i class="fas fa-users fa-2x d-block mb-2"></i>
                            <strong>Data Customer</strong>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('products.index') }}" class="btn btn-warning w-100 py-3">
                            <i class="fas fa-box fa-2x d-block mb-2"></i>
                            <strong>Data Produk</strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Transaksi Terakhir -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-history me-2"></i>Transaksi Terakhir Saya</span>
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-light">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Waktu</th>
                                <th>Customer</th>
                                <th>Lapangan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $transaction->kode_transaksi }}</strong>
                                </td>
                                <td>
                                    <small>{{ $transaction->created_at->format('d/m/Y') }}</small><br>
                                    <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div>{{ $transaction->customer->nama }}</div>
                                    <small class="text-muted">{{ $transaction->customer->no_telp }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $transaction->lapangan->nama }}</span>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ ucfirst($transaction->payment_method) }}</small>
                                </td>
                                <td>
                                    @if($transaction->status_booking == 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($transaction->status_booking == 'selesai')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Selesai
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('transactions.print', $transaction->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       target="_blank"
                                       title="Print Struk">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada transaksi hari ini</p>
                                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus me-1"></i> Buat Transaksi Pertama
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Jadwal Booking Hari Ini -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar me-2"></i>Booking Hari Ini</span>
                <span class="badge bg-light text-dark">{{ $bookingsToday->count() }}</span>
            </div>
            <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                @forelse($bookingsToday->sortBy('jam_mulai') as $booking)
                <div class="p-3 border-bottom hover-bg-light">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">{{ $booking->customer->nama }}</h6>
                            <div class="d-flex align-items-center text-muted small mb-1">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <span>{{ $booking->lapangan->nama }}</span>
                            </div>
                        </div>
                        @if($booking->status_booking == 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($booking->status_booking == 'selesai')
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="badge bg-danger">Batal</span>
                        @endif
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center text-primary">
                            <i class="fas fa-clock me-2"></i>
                            <strong>{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}</strong>
                        </div>
                        <small class="text-muted">{{ $booking->durasi_jam }} jam</small>
                    </div>
                    
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Total</small>
                            <strong class="text-success">Rp {{ number_format($booking->total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Belum ada booking hari ini</p>
                    <small class="text-muted">Jadwal masih kosong</small>
                </div>
                @endforelse
            </div>
            
            @if($bookingsToday->count() > 0)
            <div class="card-footer text-center">
                <a href="{{ route('bookings.index') }}" class="text-decoration-none">
                    Lihat Jadwal Lengkap <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Info & Tips -->
<div class="row">
    <div class="col-12">
        <div class="card border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-2x text-info"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-2">Tips Kasir</h6>
                        <ul class="mb-0 small">
                            <li>Selalu cek ketersediaan lapangan sebelum membuat booking</li>
                            <li>Pastikan customer datang 10 menit sebelum jadwal main</li>
                            <li>Print struk untuk diberikan kepada customer sebagai bukti booking</li>
                            <li>Update status booking menjadi "Selesai" setelah customer selesai bermain</li>
                            <li>Cek stok produk secara berkala untuk memastikan ketersediaan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa;
        transition: background-color 0.3s;
    }
    
    .border-4 {
        border-width: 4px !important;
    }
    
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    .btn-primary, .btn-info, .btn-success, .btn-warning {
        transition: all 0.3s;
    }
    
    .btn-primary:hover, .btn-info:hover, .btn-success:hover, .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .table tbody tr {
        transition: background-color 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto refresh setiap 5 menit untuk update data booking
    setTimeout(function() {
        location.reload();
    }, 300000); // 5 menit

    // Tampilkan notifikasi jika ada booking baru dalam 30 menit ke depan
    $(document).ready(function() {
        const now = new Date();
        const upcoming = @json($bookingsToday->filter(function($booking) {
            $jamMulai = \Carbon\Carbon::parse($booking->tanggal_main . ' ' . $booking->jam_mulai);
            $diff = $jamMulai->diffInMinutes(\Carbon\Carbon::now());
            return $diff > 0 && $diff <= 30 && $booking->status_booking == 'pending';
        })->values());
        
        if (upcoming.length > 0) {
            let message = 'Segera ada booking:\n\n';
            upcoming.forEach(function(booking) {
                message += `- ${booking.customer.nama} (${booking.lapangan.nama}) jam ${booking.jam_mulai.substring(0, 5)}\n`;
            });
            
            // Tampilkan alert (bisa diganti dengan toast notification yang lebih bagus)
            console.log(message);
        }
    });
</script>
@endpush