@extends('layouts.app')

@section('title', 'Edit Diskon / Voucher')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-edit me-2"></i>Edit Diskon / Voucher
    </div>
    <div class="card-body">
        <form action="{{ route('discounts.update', $discount->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Kode Voucher</label>
                    <input type="text" name="kode_voucher" class="form-control" value="{{ $discount->kode_voucher }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Jenis Diskon</label>
                    <select name="type" class="form-control">
                        <option value="percent" {{ $discount->type == 'percent' ? 'selected' : '' }}>Persen (%)</option>
                        <option value="amount" {{ $discount->type == 'amount' ? 'selected' : '' }}>Nominal (Rp)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Nilai Diskon</label>
                    <input type="number" step="0.01" name="value" class="form-control" value="{{ $discount->value }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ $discount->tanggal_mulai }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ $discount->tanggal_selesai }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Maksimal Penggunaan</label>
                    <input type="number" name="max_penggunaan" class="form-control" value="{{ $discount->max_penggunaan }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <select name="aktif" class="form-control">
                        <option value="1" {{ $discount->aktif ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$discount->aktif ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('discounts.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
