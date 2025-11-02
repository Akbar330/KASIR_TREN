@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-receipt me-2"></i>Riwayat Transaksi</span>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-2"></i>Transaksi Baru
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" class="form-control form-control-sm" id="filterDate" placeholder="Filter Tanggal">
            </div>
            <div class="col-md-3">
                <select class="form-control form-control-sm" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control form-control-sm" id="filterPayment">
                    <option value="">Semua Pembayaran</option>
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-secondary w-100" onclick="resetFilter()">
                    <i class="fas fa-redo me-1"></i>Reset Filter
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Lapangan</th>
                        <th>Jadwal Main</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Kasir</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td><strong class="text-primary">{{ $transaction->kode_transaksi }}</strong></td>
                        <td>
                            {{ $transaction->created_at->format('d/m/Y') }}<br>
                            <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            {{ $transaction->customer->nama }}<br>
                            <small class="text-muted">{{ $transaction->customer->no_telp }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $transaction->lapangan->nama }}</span></td>
                        <td>
                            {{ \Carbon\Carbon::parse($transaction->tanggal_main)->format('d/m/Y') }}<br>
                            <small class="text-muted">{{ substr($transaction->jam_mulai, 0, 5) }} - {{ substr($transaction->jam_selesai, 0, 5) }}</small>
                        </td>
                        <td><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
                        <td><span class="badge bg-info">{{ strtoupper($transaction->payment_method) }}</span></td>
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
                        <td>{{ $transaction->user->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('transactions.print', $transaction->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       target="_blank"
                                       title="Print Struk">
                                        <i class="fas fa-print"></i>
                                    </a>
                            <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin mau hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">Belum ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function resetFilter() {
    document.getElementById('filterDate').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterPayment').value = '';
    // Di sini bisa lo tambahin fetch ulang data via AJAX kalau mau
}
</script>
@endsection
