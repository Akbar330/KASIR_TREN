@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h2 class="fw-bold mb-4">Laporan Penjualan Produk</h2>

    {{-- Filter tanggal --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('reports.product') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan Statistik --}}
    @php
        $totalQty = $productSales->sum('qty_terjual');
        $totalPenjualan = $productSales->sum('total_penjualan');
    @endphp
    <div class="row text-center mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted mb-1">Total Produk Terjual</h6>
                <h4 class="fw-bold text-primary">{{ $totalQty }}</h4>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted mb-1">Total Pendapatan Produk</h6>
                <h4 class="fw-bold text-success">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    {{-- Tabel Laporan Produk --}}
    <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(90deg, #36b9cc, #117a8b);">
            <h6 class="mb-0">Daftar Penjualan Produk</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Qty Terjual</th>
                        <th>Total Penjualan</th>
                        <th>Stok Tersisa</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($productSales as $produk)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $produk['nama'] }}</td>
                            <td>{{ $produk['kategori'] }}</td>
                            <td>{{ $produk['qty_terjual'] }}</td>
                            <td>Rp {{ number_format($produk['total_penjualan'], 0, ',', '.') }}</td>
                            <td>{{ $produk['stok_tersisa'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Tidak ada data penjualan produk pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
