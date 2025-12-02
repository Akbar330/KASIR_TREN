@extends('layouts.app')

@section('title', 'Buat Diskon Baru')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus me-2"></i>Buat Diskon / Voucher Baru
        </div>
        <div class="card-body">
            <form action="{{ route('discounts.store') }}" method="POST">
                @csrf

                <label>Kode Voucher</label>
                <input type="text" name="kode_voucher" class="form-control">

                <label>Jenis</label>
                <select name="type" class="form-control">
                    <option value="percent">Persen</option>
                    <option value="amount">Nominal</option>
                </select>

                <label>Nilai</label>
                <input type="number" step="0.01" name="value" class="form-control">

                <label>Mulai Berlaku</label>
                <input type="date" name="tanggal_mulai" class="form-control">

                <label>Berakhir</label>
                <input type="date" name="tanggal_selesai" class="form-control">

                {{-- <label>Kuota</label>
                <input type="number" name="max_penggunaan" class="form-control"> --}}

                <button type="submit" class="btn btn-primary mt-3">Simpan</button>
            </form>


        </div>
    </div>
@endsection
