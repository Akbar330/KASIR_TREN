@extends('layouts.app')

@section('title', 'Permintaan Pembatalan Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4"><div><p class="text-warning fw-bold small text-uppercase mb-1">Perlu ditinjau</p><h2 class="fw-bold mb-1" style="letter-spacing:-.04em">Permintaan pembatalan</h2><p class="text-muted mb-0">Periksa permintaan kasir sebelum mengubah status transaksi.</p></div><span class="badge bg-warning">{{ $transactions->count() }} permintaan</span></div>
<div class="card">
    <div class="card-header"><strong>Antrean persetujuan</strong><div class="text-muted small fw-normal mt-1">Persetujuan akan mengembalikan stok produk terkait.</div></div>
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Customer</th>
                    <th>Kasir</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                <td><strong class="text-success">{{ $transaction->kode_transaksi }}</strong><small class="d-block text-muted">{{ $transaction->lapangan->nama ?? '-' }}</small></td>
                    <td>{{ $transaction->customer->nama ?? '-' }}</td>
                    <td>{{ $transaction->user->name ?? '-' }}</td>
                    <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <form action="{{ route('transactions.approve-cancel', $transaction->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5"><i class="fas fa-circle-check fa-2x d-block mb-3 text-success"></i>Tidak ada permintaan pembatalan yang menunggu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
