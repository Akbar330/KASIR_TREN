@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h2 class="fw-bold mb-4">Laporan Omset</h2>

    {{-- Filter tanggal --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('reports.omset') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('reports.export-omset', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success w-100">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted mb-1">Total Omset</h6>
                <h4 class="fw-bold text-success">Rp {{ number_format($totalOmset, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted mb-1">Total Transaksi</h6>
                <h4 class="fw-bold">{{ $totalTransaksi }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted mb-1">Total Lapangan</h6>
                <h4 class="fw-bold text-primary">Rp {{ number_format($totalLapangan, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted mb-1">Total Produk</h6>
                <h4 class="fw-bold text-warning">Rp {{ number_format($totalProduk, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    {{-- Omset per Hari --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(90deg, #4e73df, #224abe);">
            <h6 class="mb-0">Omset Per Hari</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Total Omset</th>
                        <th>Jumlah Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($omsetPerHari as $data)
                        <tr>
                            <td>{{ $data['tanggal'] }}</td>
                            <td>Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                            <td>{{ $data['count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada data omset pada rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Omset per Lapangan --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(90deg, #1cc88a, #13855c);">
            <h6 class="mb-0">Omset Per Lapangan</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Lapangan</th>
                        <th>Total Pendapatan</th>
                        <th>Jumlah Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($omsetPerLapangan as $data)
                        <tr>
                            <td>{{ $data['lapangan'] }}</td>
                            <td>Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                            <td>{{ $data['count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Belum ada transaksi untuk lapangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail Transaksi --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(90deg, #f6c23e, #dda20a);">
            <h6 class="mb-0">Detail Transaksi</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Lapangan</th>
                        <th>Durasi</th>
                        <th>Subtotal Lapangan</th>
                        <th>Subtotal Produk</th>
                        <th>Total</th>
                        <th>Kasir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $trx)
                        <tr>
                            <td>{{ $trx->kode_transaksi }}</td>
                            <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $trx->customer->nama }}</td>
                            <td>{{ $trx->lapangan->nama }}</td>
                            <td>{{ $trx->durasi_jam }} jam</td>
                            <td>Rp {{ number_format($trx->subtotal_lapangan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($trx->subtotal_produk, 0, ',', '.') }}</td>
                            <td class="fw-bold text-success">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                            <td>{{ $trx->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
