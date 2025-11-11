@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Transaksi</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Customer</label>
                <select name="customer_id" class="form-control">
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" 
                            {{ $transaction->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Tanggal Main</label>
                <input type="date" name="tanggal_main" value="{{ $transaction->tanggal_main }}" class="form-control">
            </div>

            <div class="text-end">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
