@extends('layouts.app')

@section('title', 'Permintaan Pembatalan Transaksi')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-ban me-2"></i>Permintaan Pembatalan dari Kasir
    </div>
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
                    <td><strong>{{ $transaction->kode_transaksi }}</strong></td>
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
                    <td colspan="6" class="text-center text-muted">Tidak ada permintaan pembatalan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
