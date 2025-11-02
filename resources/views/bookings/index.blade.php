@extends('layouts.app')

@section('title', 'Jadwal Booking')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-calendar-alt me-2"></i>Jadwal Booking</span>
        <a href="{{ route('bookings.calendar') }}" class="btn btn-sm btn-light">
            <i class="fas fa-calendar me-2"></i>Tampilan Kalender
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Lapangan</label>
                <select name="lapangan_id" class="form-control">
                    <option value="">Semua Lapangan</option>
                    @foreach($lapangans as $lap)
                    <option value="{{ $lap->id }}" {{ $lapangan_id == $lap->id ? 'selected' : '' }}>
                        {{ $lap->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
        
        <!-- Jadwal Grid -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="120">Jam</th>
                        @foreach($lapangans as $lap)
                        <th class="text-center">{{ $lap->nama }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal as $jam)
                    <tr>
                        <td class="fw-bold">{{ $jam }}</td>
                        @foreach($lapangans as $lap)
                        @php
                            $booking = $bookings->first(function($b) use ($lap, $jam) {
                                return $b->lapangan_id == $lap->id && 
                                       $jam >= substr($b->jam_mulai, 0, 5) && 
                                       $jam < substr($b->jam_selesai, 0, 5);
                            });
                        @endphp
                        <td class="text-center {{ $booking ? 'table-danger' : 'table-success' }}">
                            @if($booking)
                                <div class="small">
                                    <strong>{{ $booking->customer->nama }}</strong><br>
                                    {{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}
                                </div>
                            @else
                                <span class="text-success">✓ Tersedia</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Daftar Booking -->
        <h5 class="mt-4 mb-3">Daftar Booking {{ date('d F Y', strtotime($tanggal)) }}</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Customer</th>
                        <th>Lapangan</th>
                        <th>Jam</th>
                        <th>Durasi</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td>{{ $booking->kode_transaksi }}</td>
                        <td>{{ $booking->customer->nama }}</td>
                        <td>{{ $booking->lapangan->nama }}</td>
                        <td>{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}</td>
                        <td>{{ $booking->durasi_jam }} jam</td>
                        <td>Rp {{ number_format($booking->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $booking->status_booking == 'pending' ? 'warning' : ($booking->status_booking == 'selesai' ? 'success' : 'danger') }}">
                                {{ ucfirst($booking->status_booking) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('transactions.print', $booking->id) }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if($booking->status_booking == 'pending')
                                <button class="btn btn-sm btn-success" onclick="updateStatus({{ $booking->id }}, 'selesai')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="updateStatus({{ $booking->id }}, 'dibatalkan')">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada booking</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(id, status) {
    if (confirm('Yakin ingin mengubah status booking?')) {
        $.ajax({
            url: `/transactions/${id}/status`,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status_booking: status
            },
            success: function() {
                location.reload();
            }
        });
    }
}
</script>
@endpush