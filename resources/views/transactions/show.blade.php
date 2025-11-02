@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-info-circle me-2"></i>Detail Transaksi</span>
        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="fw-bold mb-3 text-primary">Informasi Transaksi</h5>
                <table class="table table-borderless">
                    <tr>
                        <th>Kode Transaksi</th>
                        <td>{{ $transaction->kode_transaksi }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($transaction->status_booking == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($transaction->status_booking == 'selesai')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($transaction->status_booking == 'dibatalkan')
                                <span class="badge bg-danger">Dibatalkan</span>
                            @else
                                <span class="badge bg-secondary">Tidak Diketahui</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Metode Pembayaran</th>
                        <td><span class="badge bg-info">{{ strtoupper($transaction->payment_method) }}</span></td>
                    </tr>
                    <tr>
                        <th>Total Bayar</th>
                        <td><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h5 class="fw-bold mb-3 text-primary">Informasi Pemesan</h5>
                <table class="table table-borderless">
                    <tr>
                        <th>Nama Customer</th>
                        <td>{{ $transaction->customer->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td>{{ $transaction->customer->no_telp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Kasir</th>
                        <td>{{ $transaction->user->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <div class="row mb-4">
            <div class="col-md-12">
                <h5 class="fw-bold mb-3 text-primary">Detail Booking Lapangan</h5>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Lapangan</th>
                            <th>Tanggal Main</th>
                            <th>Jam Main</th>
                            <th>Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $transaction->lapangan->nama ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction->tanggal_main)->format('d/m/Y') }}</td>
                            <td>{{ substr($transaction->jam_mulai, 0, 5) }} - {{ substr($transaction->jam_selesai, 0, 5) }}</td>
                            <td>
                                @php
                                    $mulai = \Carbon\Carbon::parse($transaction->jam_mulai);
                                    $selesai = \Carbon\Carbon::parse($transaction->jam_selesai);
                                    $durasi = $selesai->diffInHours($mulai);
                                @endphp
                                {{ $durasi }} Jam
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-end">
            <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin mau hapus transaksi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
