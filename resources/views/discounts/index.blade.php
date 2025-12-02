@extends('layouts.app')

@section('title', 'Daftar Diskon / Voucher')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-tags me-2"></i>Daftar Diskon / Voucher</span>
        <a href="{{ route('discounts.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-2"></i>Buat Diskon Baru
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Kode Voucher</th>
                        <th>Jenis</th>
                        <th>Nilai</th>
                        <th>Periode</th>
                        <th>Status</th>
                        {{-- <th>Dipakai</th> --}}
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discounts as $discount)
                        <tr>
                            <td><strong class="text-primary">{{ $discount->kode_voucher }}</strong></td>
                            <td>
                                @if($discount->type == 'percent')
                                    <span class="badge bg-info">Persen</span>
                                @else
                                    <span class="badge bg-success">Nominal</span>
                                @endif
                            </td>
                            <td>
                                @if($discount->type == 'percent')
                                    {{ $discount->value }}%
                                @else
                                    Rp {{ number_format($discount->value, 0, ',', '.') }}
                                @endif
                            </td>
                            <td>
                                {{ $discount->tanggal_mulai ?? '-' }} s/d 
                                {{ $discount->tanggal_selesai ?? '-' }}
                            </td>
                            <td>
                                @if($discount->aktif)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            {{-- <td>{{ $discount->digunakan }}/{{ $discount->max_penggunaan }}</td> --}}
                            <td>
                                <a href="{{ route('discounts.edit', $discount->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin mau hapus voucher ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada voucher, jir</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
