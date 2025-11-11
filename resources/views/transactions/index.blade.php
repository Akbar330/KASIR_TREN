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
                                    <small class="text-muted">{{ substr($transaction->jam_mulai, 0, 5) }} -
                                        {{ substr($transaction->jam_selesai, 0, 5) }}</small>
                                </td>
                                <td><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
                                <td><span class="badge bg-info">{{ strtoupper($transaction->payment_method) }}</span></td>
                                <td>
                                    @if ($transaction->status_booking == 'pending')
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
                                        class="btn btn-sm btn-info" target="_blank" title="Print Struk">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('transactions.show', $transaction->id) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('transactions.edit', $transaction->id) }}"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin membatalkan transaksi ini?')">
                                        @csrf
                                        @method('DELETE')

                                        @if (Auth::user()->role === 'Admin')
                                            <button class="btn btn-sm btn-danger" title="Batalkan langsung">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-warning"
                                                title="Minta persetujuan pembatalan ke admin">
                                                <i class="fas fa-hourglass-half"></i>
                                            </button>
                                        @endif
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            let productIndex = 1;
            let hargaPerJam = 0;
            let totalLapangan = 0;
            let totalProduk =
                0;
            Cek ketersediaan lapangan

            function checkAvailability() {
                const lapangan_id = $('#lapangan_id').val();
                const tanggal_main = $('#tanggal_main').val();
                const jam_mulai = $('#jam_mulai').val();
                const jam_selesai = $('#jam_selesai').val();
                if (lapangan_id && tanggal_main && jam_mulai && jam_selesai) {
                    $.post('{{ route('transactions.check-availability') }}', {
                        _token: '{{ csrf_token() }}',
                        lapangan_id,
                        tanggal_main,
                        jam_mulai,
                        jam_selesai
                    }, function(response) {
                        const alert = $('#availabilityAlert');
                        if (response.available) {
                            alert.removeClass('d-none alert-danger').addClass('alert-success');
                            alert.html('<i class="fas fa-check-circle me-2"></i>' + response.message);
                            $('#submitBtn').prop('disabled', false);
                        } else {
                            alert.removeClass('d-none alert-success').addClass('alert-danger');
                            alert.html('<i class="fas fa-exclamation-circle me-2"></i>' + response.message);
                            $('#submitBtn').prop('disabled', true);
                        }
                    });
                }
            }
            Hitung subtotal lapangan

            function calculateLapangan() {
                const jam_mulai = $('#jam_mulai').val();
                const jam_selesai = $('#jam_selesai').val();
                if (jam_mulai && jam_selesai && hargaPerJam > 0) {
                    const start = new Date('2000-01-01 ' + jam_mulai);
                    const end = new Date('2000-01-01 ' + jam_selesai);
                    const diffHours = (end - start) / (1000 * 60 * 60);
                    if (diffHours > 0) {
                        totalLapangan = diffHours * hargaPerJam;
                        $('#subtotalLapangan').text('Rp ' + totalLapangan.toLocaleString('id-ID'));
                        calculateTotal();
                    }
                }
            }
            Hitung subtotal produk

            function calculateProduk() {
                totalProduk = 0;
                $('.product-item').each(function() {
                    const qty = $(this).find('.product-qty').val();
                    const harga = $(this).find('.product-select option:selected').data('harga');
                    if (qty && harga) {
                        const subtotal = qty * harga;
                        totalProduk += subtotal;
                        $(this).find('.product-subtotal').val('Rp ' + subtotal.toLocaleString('id-ID'));
                    }
                });
                $('#subtotalProduk').text('Rp ' + totalProduk.toLocaleString('id-ID'));
                calculateTotal();
            }
            Hitung total

            function calculateTotal() {
                const total = totalLapangan + totalProduk;
                $('#totalBayar').text('Rp ' + total.toLocaleString('id-ID'));
                calculateKembalian();
            }
            Hitung kembalian

            function calculateKembalian() {
                const bayar = parseFloat($('#bayar').val()) || 0;
                const total = totalLapangan + totalProduk;
                const kembalian = bayar - total;
                $('#kembalian').text('Rp ' + kembalian.toLocaleString('id-ID'));
                $('#kembalian').toggleClass('text-danger', kembalian < 0);
                $('#kembalian').toggleClass('text-success', kembalian >= 0);
            }
            Event listeners $('#lapangan_id').change(function() {
                hargaPerJam = $(this).find(':selected').data('harga') || 0;
                calculateLapangan();
                checkAvailability();
            });
            $('#tanggal_main, #jam_mulai, #jam_selesai').change(function() {
                calculateLapangan();
                checkAvailability();
            });
            $('#bayar').on('input',
                calculateKembalian);
            Tambah produk $('#addProduct').click(function() {
                const newProduct = $('.product-item:first').clone();
                newProduct.find('select, input').val('');
                newProduct.find('select').attr('name', products[$ {
                    productIndex
                }][product_id]);
                newProduct.find('.product-qty').attr('name', products[$ {
                    productIndex
                }][qty]);
                $('#productList').append(newProduct);
                productIndex++;
            });
            Hapus produk $(document).on('click', '.btn-remove-product', function() {
                if ($('.product-item').length > 1) {
                    $(this).closest('.product-item').remove();
                    calculateProduk();
                }
            });
            Hitung produk saat input $(document).on('change', '.product-select, .product-qty',
                calculateProduk);
            Tambah customer via modal $('#addCustomerForm').submit(function(e) {
                e.preventDefault();
                $.post('{{ route('customers.store') }}', $(this).serialize(), function(response) {
                    const newOption = new Option(response.nama + ' - ' + response.no_telp, response
                        .id, true, true);
                    $('#customer_id').append(newOption).trigger('change');
                    $('#addCustomerModal').modal('hide');
                    $('#addCustomerForm')[0].reset();
                });
            });
        });
    </script>
@endpush
