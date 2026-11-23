@extends('layouts.app')

@section('title', 'Ringkasan Operasional')

@section('content')
<div class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4">
    <div><p class="text-success fw-bold small text-uppercase mb-1">Pusat kendali</p><h2 class="fw-bold mb-1" style="letter-spacing:-.04em">Selamat datang, {{ auth()->user()->name }}</h2><p class="text-muted mb-0">Pantau performa dan aktivitas operasional hari ini.</p></div>
    <a href="{{ route('transactions.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Transaksi baru</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body d-flex justify-content-between align-items-start"><div><div class="text-muted small fw-semibold mb-2">Omset hari ini</div><div class="fs-4 fw-bold">Rp {{ number_format($omsetToday,0,',','.') }}</div><small class="text-muted">Pendapatan tercatat</small></div><span class="rounded-3 p-3" style="background:#edf0ff;color:#4a6de0"><i class="fas fa-wallet fa-lg"></i></span></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body d-flex justify-content-between align-items-start"><div><div class="text-muted small fw-semibold mb-2">Omset bulan ini</div><div class="fs-4 fw-bold">Rp {{ number_format($omsetMonth,0,',','.') }}</div><small class="text-muted">Akumulasi bulan berjalan</small></div><span class="rounded-3 p-3" style="background:#edf0ff;color:#4a6de0"><i class="fas fa-chart-line fa-lg"></i></span></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body d-flex justify-content-between align-items-start"><div><div class="text-muted small fw-semibold mb-2">Transaksi hari ini</div><div class="fs-4 fw-bold">{{ $transaksiToday }}</div><small class="text-muted">Transaksi tersimpan</small></div><span class="rounded-3 p-3" style="background:#edf0ff;color:#4a6de0"><i class="fas fa-receipt fa-lg"></i></span></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body d-flex justify-content-between align-items-start"><div><div class="text-muted small fw-semibold mb-2">Booking hari ini</div><div class="fs-4 fw-bold">{{ $bookingsToday->count() }}</div><small class="text-muted">Jadwal lapangan aktif</small></div><span class="rounded-3 p-3" style="background:#fff3d9;color:#aa7618"><i class="fas fa-calendar-check fa-lg"></i></span></div></div></div>
</div>
<div class="row g-3 mb-3">
    <div class="col-xl-8"><div class="card h-100"><div class="card-header d-flex justify-content-between align-items-center"><div><strong>Performa omset</strong><div class="text-muted small fw-normal mt-1">Ringkasan tujuh hari terakhir</div></div><span class="badge bg-success">7 hari</span></div><div class="card-body"><div style="height:290px"><canvas id="omsetChart"></canvas></div></div></div></div>
    <div class="col-xl-4"><div class="card h-100"><div class="card-header"><strong>Lapangan terpopuler</strong><div class="text-muted small fw-normal mt-1">Booking dengan frekuensi tertinggi</div></div><div class="card-body p-0"><div class="list-group list-group-flush">@forelse($popularLapangan as $index=>$item)<div class="list-group-item d-flex align-items-center gap-3 py-3"><span class="rounded-circle d-grid place-items-center fw-bold" style="width:36px;height:36px;background:#edf0ff;color:#4a6de0">{{ $index+1 }}</span><div class="flex-grow-1 fw-semibold">{{ $item->lapangan->nama }}</div><span class="badge bg-success">{{ $item->total }} booking</span></div>@empty<div class="text-center text-muted py-5">Belum ada data booking.</div>@endforelse</div></div></div></div>
</div>
<div class="row g-3">
    <div class="col-xl-8"><div class="card"><div class="card-header d-flex justify-content-between align-items-center"><div><strong>Jadwal hari ini</strong><div class="text-muted small fw-normal mt-1">Booking yang perlu dipantau</div></div><a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-success">Buka jadwal<i class="fas fa-arrow-right ms-2"></i></a></div><div class="table-responsive"><table class="table table-hover"><thead><tr><th>Waktu</th><th>Lapangan</th><th>Pelanggan</th><th>Status</th></tr></thead><tbody>@forelse($bookingsToday as $booking)<tr><td class="fw-semibold">{{ substr($booking->jam_mulai,0,5) }} â€“ {{ substr($booking->jam_selesai,0,5) }}</td><td>{{ $booking->lapangan->nama }}</td><td>{{ $booking->customer->nama }}</td><td><span class="badge bg-warning">{{ ucfirst($booking->status_booking) }}</span></td></tr>@empty<tr><td colspan="4" class="text-center py-5 text-muted">Belum ada booking untuk hari ini.</td></tr>@endforelse</tbody></table></div></div></div>
    <div class="col-xl-4"><div class="card h-100"><div class="card-header d-flex justify-content-between align-items-center"><strong>Stok perlu perhatian</strong><a href="{{ route('products.index') }}" class="small text-success text-decoration-none">Lihat produk</a></div><div class="card-body p-0"><div class="list-group list-group-flush">@forelse($lowStockProducts as $product)<div class="list-group-item d-flex justify-content-between align-items-center py-3"><span><i class="fas fa-box-open text-muted me-2"></i>{{ $product->nama }}</span><span class="badge bg-danger">{{ $product->stok }} tersisa</span></div>@empty<div class="text-center text-muted py-5"><i class="fas fa-circle-check text-success me-2"></i>Semua stok tersedia.</div>@endforelse</div></div></div></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartCanvas=document.getElementById('omsetChart');
if(chartCanvas){new Chart(chartCanvas,{type:'line',data:{labels:{!! json_encode(array_column($omsetChart,'date')) !!},datasets:[{label:'Omset',data:{!! json_encode(array_column($omsetChart,'total')) !!},borderColor:'#4a6de0',backgroundColor:'rgba(74,109,224,.12)',pointBackgroundColor:'#4a6de0',pointRadius:3,tension:.38,fill:true}]},options:{maintainAspectRatio:false,responsive:true,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{color:'#819087'}},y:{beginAtZero:true,grid:{color:'#edf1ee'},ticks:{color:'#819087',callback:value=>'Rp '+Number(value).toLocaleString('id-ID')}}}}})}
</script>
@endpush
