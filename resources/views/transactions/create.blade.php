@extends('layouts.app')

@section('title', 'Transaksi Baru')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-2"></i>Buat Transaksi Baru
        </div>
        <div class="card-body">
            <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                @csrf

                <div class="row">
                    <!-- Customer -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="customer_id" id="customer_id" class="form-control select2" required>
                                <option value="">Pilih Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->nama }} - {{ $customer->no_telp }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addCustomerModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Lapangan -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lapangan <span class="text-danger">*</span></label>
                        <select name="lapangan_id" id="lapangan_id" class="form-control" required>
                            <option value="">Pilih Lapangan</option>

                            @foreach ($lapangan as $jenis => $listLapangan)
                                <optgroup label="{{ ucfirst(str_replace('_', ' ', $jenis)) }}">
                                    @foreach ($listLapangan as $lap)
                                        <option value="{{ $lap->id }}" data-harga="{{ $lap->harga_per_jam }}">
                                            {{ $lap->nama }} (Rp
                                            {{ number_format($lap->harga_per_jam, 0, ',', '.') }}/jam)
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach

                        </select>
                    </div>

                    <!-- Tanggal Main -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Main <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_main" id="tanggal_main" class="form-control"
                            min="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Jam Mulai -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" required>
                    </div>

                    <!-- Jam Selesai -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required>
                    </div>
                </div>

                <div id="availabilityAlert" class="alert d-none"></div>

                <hr>

                <h5 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Produk Tambahan</h5>

                <div id="productList">
                    <div class="row product-item mb-2">
                        <div class="col-md-5">
                            <select name="products[0][product_id]" class="form-control product-select">
                                <option value="">Pilih Produk (Opsional)</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-harga="{{ $product->harga }}"
                                        data-stok="{{ $product->stok }}">
                                        {{ $product->nama }} - Rp {{ number_format($product->harga, 0, ',', '.') }} (Stok:
                                        {{ $product->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="products[0][qty]" class="form-control product-qty" placeholder="Qty"
                                min="1">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control product-subtotal" readonly placeholder="Subtotal">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-remove-product">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-sm mb-3" id="addProduct">
                    <i class="fas fa-plus me-2"></i>Tambah Produk
                </button>

                <hr>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Pilih Diskon</label>
                    <select name="discount_id" id="discount_id" class="form-control">
                        <option value="">Tanpa Diskon</option>
                        @foreach ($discounts as $diskon)
                            <option value="{{ $diskon->id }}" data-type="{{ $diskon->type }}"
                                data-value="{{ $diskon->value }}">
                                {{ $diskon->kode_voucher }}
                                ({{ $diskon->type == 'percent' ? $diskon->value . '%' : 'Rp ' . number_format($diskon->value, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal Lapangan:</span>
                                    <strong id="subtotalLapangan">Rp 0</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal Produk:</span>
                                    <strong id="subtotalProduk">Rp 0</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Diskon:</span>
                                    <strong id="diskonPersen">0%</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Potongan Harga:</span>
                                    <strong id="potonganHarga">Rp 0</strong>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <h5>Total:</h5>
                                    <h5 class="text-primary" id="totalBayar">Rp 0</h5>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                                    <input type="number" name="bayar" id="bayar" class="form-control" required>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <span>Kembalian:</span>
                                    <strong id="kembalian" class="text-success">Rp 0</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Customer -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Customer Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCustomerForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telp</label>
                            <input type="text" name="no_telp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            let productIndex = 1;
            let hargaPerJam = 0;
            let totalLapangan = 0;
            let totalProduk = 0;

            // ==============================
            // CEK KETERSEDIAAN LAPANGAN
            // ==============================
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

            // ==============================
            // HITUNG SUBTOTAL LAPANGAN
            // ==============================
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

            // ==============================
            // HITUNG SUBTOTAL PRODUK
            // ==============================
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

            // ==============================
            // HITUNG TOTAL (DENGAN DISKON)
            // ==============================
            function calculateTotal() {
                const subtotal = totalLapangan + totalProduk;

                // Ambil data diskon
                const selected = $('#discount_id').find(':selected');
                const type = selected.data('type');
                const value = parseFloat(selected.data('value')) || 0;

                let potongan = 0;
                let persenDiskon = 0;

                if (type === 'percent') {
                    persenDiskon = value;
                    potongan = subtotal * (value / 100);
                } else if (type === 'amount') {
                    potongan = value;
                    // Hitung persen dari potongan
                    if (subtotal > 0) {
                        persenDiskon = (potongan / subtotal) * 100;
                    }
                }

                const total = subtotal - potongan;

                // Update tampilan
                $('#diskonPersen').text(persenDiskon.toFixed(1) + '%');
                $('#potonganHarga').text('Rp ' + potongan.toLocaleString('id-ID'));
                $('#totalBayar').text('Rp ' + total.toLocaleString('id-ID'));

                // ✅ Simpan nilai untuk dikirim ke database
                updateHiddenInputs(subtotal, potongan, total);

                // Hitung kembalian jika ada input bayar
                calculateKembalian();
            }

            // ==============================
            // UPDATE HIDDEN INPUTS
            // ==============================
            function updateHiddenInputs(subtotal, potongan, total) {
                // Hapus input lama jika ada
                $('#hiddenSubtotal, #hiddenDiscount, #hiddenTotal').remove();

                // Tambahkan hidden inputs baru
                $('#transactionForm').append(`
            <input type="hidden" name="subtotal" id="hiddenSubtotal" value="${subtotal}">
            <input type="hidden" name="discount_amount" id="hiddenDiscount" value="${potongan}">
            <input type="hidden" name="total_amount" id="hiddenTotal" value="${total}">
        `);
            }

            // ==============================
            // HITUNG KEMBALIAN
            // ==============================
            function calculateKembalian() {
                const totalText = $('#totalBayar').text().replace(/[^0-9]/g, '');
                const total = parseFloat(totalText) || 0;
                const bayar = parseFloat($('#bayar').val()) || 0;

                const kembalian = bayar - total;

                if (kembalian >= 0) {
                    $('#kembalian').text('Rp ' + kembalian.toLocaleString('id-ID'));
                    $('#kembalian').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#kembalian').text('Kurang Rp ' + Math.abs(kembalian).toLocaleString('id-ID'));
                    $('#kembalian').removeClass('text-success').addClass('text-danger');
                }
            }

            // ==============================
            // EVENT LISTENERS
            // ==============================
            $('#lapangan_id').change(function() {
                hargaPerJam = $(this).find(':selected').data('harga') || 0;
                calculateLapangan();
                checkAvailability();
            });

            $('#tanggal_main, #jam_mulai, #jam_selesai').change(function() {
                calculateLapangan();
                checkAvailability();
            });

            $('#bayar').on('input', calculateKembalian);

            // Event listener untuk diskon
            $('#discount_id').change(function() {
                calculateTotal();
            });

            // Tambah produk
            $('#addProduct').click(function() {
                const newProduct = $('.product-item:first').clone();
                newProduct.find('select, input').val('');
                newProduct.find('select').attr('name', `products[${productIndex}][product_id]`);
                newProduct.find('.product-qty').attr('name', `products[${productIndex}][qty]`);
                $('#productList').append(newProduct);
                productIndex++;
            });

            // Hapus produk
            $(document).on('click', '.btn-remove-product', function() {
                if ($('.product-item').length > 1) {
                    $(this).closest('.product-item').remove();
                    calculateProduk();
                }
            });

            // Hitung produk saat input berubah
            $(document).on('change', '.product-select, .product-qty', calculateProduk);

            // Tambah customer via modal
            $('#addCustomerForm').submit(function(e) {
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
