@extends('layouts.app')

@section('title', 'Kasir')

@push('styles')
<style data-page-style>
    .pos-page{--pos-ink:#171718;--pos-muted:#777985;--pos-line:#e6e9f2;--pos-green:#4a6de0;--pos-soft:#f4f5f7;color:var(--pos-ink)}
    .pos-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1.4rem}
    .pos-heading h2{font-size:1.55rem;font-weight:800;letter-spacing:-.04em;margin:0}.pos-heading p{color:var(--pos-muted);margin:.35rem 0 0}
    .pos-live{display:flex;align-items:center;gap:.55rem;color:#555761;font-size:.85rem;font-weight:600}.pos-live:before{content:'';width:9px;height:9px;border-radius:50%;background:#d2f801;box-shadow:0 0 0 4px rgba(210,249,1,.24)}
    .transaction-success{display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;padding:1.1rem 1.2rem;border:1px solid #e6e9f2;border-left:5px solid #d2f801;border-radius:14px;background:#fff;box-shadow:0 5px 20px rgba(23,23,24,.045)}.transaction-success-icon{display:grid;place-items:center;width:44px;height:44px;flex:0 0 44px;border-radius:13px;background:#f3ffd0;color:#171718;font-size:1.15rem}.transaction-success-copy{flex:1;min-width:0}.transaction-success-copy strong{display:block;color:#171718;font-size:.96rem}.transaction-success-copy p{margin:.2rem 0 0;color:#777985;font-size:.8rem}.transaction-success-actions{display:flex;gap:.55rem;flex-wrap:wrap}
    .pos-card{border:1px solid var(--pos-line);border-radius:16px;background:white;box-shadow:0 8px 28px rgba(31,55,42,.045);overflow:hidden}
    .pos-card-head{padding:1.1rem 1.25rem;border-bottom:1px solid var(--pos-line);display:flex;align-items:center;gap:.8rem}.pos-card-head .step{width:34px;height:34px;border-radius:11px;background:#edf0ff;color:var(--pos-green);display:grid;place-items:center;font-weight:800}
    .pos-card-head h3{font-size:1rem;font-weight:750;margin:0}.pos-card-head p{font-size:.8rem;color:var(--pos-muted);margin:.2rem 0 0}.pos-card-body{padding:1.25rem}
    .pos-page .form-label{font-size:.83rem;font-weight:700;color:#37383e;margin-bottom:.45rem}.pos-page .form-control,.pos-page .form-select{border-color:#dfe1e8;border-radius:10px;min-height:44px;padding:.65rem .8rem}.pos-page .form-control:focus,.pos-page .form-select:focus{border-color:#8298ed;box-shadow:0 0 0 .2rem rgba(74,109,224,.12)}
    .field-hint{font-size:.76rem;color:var(--pos-muted);margin-top:.35rem}.product-row{display:grid;grid-template-columns:minmax(0,1fr) 94px 142px 40px;gap:.55rem;align-items:center;margin-bottom:.6rem}.product-subtotal{font-weight:700;color:#37383e;background:#f7f8fa!important}.btn-icon{width:40px;height:40px;border:1px solid #f0d8d8;background:#fff7f7;color:#ba4c4c;border-radius:10px}.btn-add{border:1px dashed #b6c2ef;color:var(--pos-green);background:#f7f8ff;border-radius:10px;font-weight:700;padding:.6rem .85rem}.btn-add:hover{background:#edf0ff}
    .summary-card{position:sticky;top:90px;border:1px solid #e6e9f2;border-radius:16px;background:white;box-shadow:0 12px 32px rgba(31,55,42,.07);overflow:hidden}.summary-top{padding:1.25rem;background:linear-gradient(140deg,#d2f801,#e4ff5b);color:#171718}.summary-top small{opacity:.75;font-weight:600}.summary-top h3{font-size:1.05rem;margin:.25rem 0 0;font-weight:750}.summary-body{padding:1.2rem}.summary-line{display:flex;justify-content:space-between;gap:1rem;margin-bottom:.85rem;color:#777985;font-size:.88rem}.summary-line strong{color:#171718}.summary-total{border-top:1px solid var(--pos-line);padding-top:1rem;margin-top:1rem;display:flex;justify-content:space-between;align-items:end}.summary-total span{font-weight:700}.summary-total strong{font-size:1.55rem;color:var(--pos-green);letter-spacing:-.04em}.change-box{padding:.8rem 1rem;border-radius:11px;background:#edf0ff;display:flex;justify-content:space-between;align-items:center;margin-top:1rem}.change-box strong{font-size:1.05rem}.change-box.is-short{background:#fff5f2;color:#b54b35}.pay-input{font-size:1.1rem;font-weight:700!important}.pay-methods{display:grid;grid-template-columns:repeat(3,1fr);gap:.45rem}.pay-option{position:relative}.pay-option input{position:absolute;opacity:0}.pay-option span{display:flex;align-items:center;justify-content:center;gap:.4rem;padding:.65rem .4rem;border:1px solid #dfe1e8;border-radius:10px;font-size:.8rem;font-weight:700;color:#555761;cursor:pointer}.pay-option input:checked+span{border-color:#4a6de0;background:#edf0ff;color:#344fa9;box-shadow:inset 0 0 0 1px #4a6de0}.checkout-btn{width:100%;border:0;border-radius:11px;background:#d2f801;color:#171718;font-weight:750;padding:.9rem 1rem;margin-top:1rem}.checkout-btn:hover{background:#c4ec00}.checkout-btn:disabled{opacity:.55;cursor:not-allowed}.availability{border-radius:10px;padding:.75rem .9rem;font-weight:600;font-size:.85rem}.availability:empty{display:none}.pos-page .select2-container{width:100%!important}.pos-page .select2-container--default .select2-selection--single{height:44px;border:1px solid #dfe1e8;border-radius:10px}.pos-page .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:42px;padding-left:12px}.pos-page .select2-container--default .select2-selection--single .select2-selection__arrow{height:42px;right:7px}
    @media(max-width:991px){.summary-card{position:static}.pos-heading{align-items:flex-start;flex-direction:column}}
    @media(max-width:575px){.product-row{grid-template-columns:minmax(0,1fr) 72px 40px}.product-row .product-subtotal{grid-column:1/3;grid-row:2}.product-row .btn-remove-product{grid-column:3;grid-row:1}.pos-card-body{padding:1rem}.pos-heading h2{font-size:1.35rem}}
</style>
@endpush

@section('content')
<div class="pos-page">
    @if(session('created_transaction_id'))
        <section class="transaction-success" role="status" aria-live="polite">
            <span class="transaction-success-icon"><i class="fas fa-check" aria-hidden="true"></i></span>
            <div class="transaction-success-copy"><strong>Transaksi berhasil dibuat</strong><p>Transaksi tersimpan. Lihat struk sekarang atau kembali ke riwayat.</p></div>
            <div class="transaction-success-actions"><a href="{{ route('transactions.print', session('created_transaction_id')) }}" class="btn btn-primary"><i class="fas fa-receipt me-2" aria-hidden="true"></i>Lihat struk</a><a href="{{ route('transactions.index') }}" class="btn btn-light border">Kembali ke riwayat</a></div>
        </section>
    @endif
    <div class="pos-heading">
        <div><h2>Kasir & transaksi</h2><p>Atur booking, produk, dan pembayaran dalam satu layar.</p></div>
        <div class="pos-live">Sesi kasir aktif | {{ now()->translatedFormat('l, d F Y') }}</div>
    </div>

    <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
        @csrf
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <section class="pos-card mb-4">
                    <div class="pos-card-head"><div class="step">1</div><div><h3>Detail booking</h3><p>Pilih pelanggan, lapangan, dan waktu bermain.</p></div></div>
                    <div class="pos-card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label" for="customer_id">Pelanggan <span class="text-danger">*</span></label><div class="input-group"><select name="customer_id" id="customer_id" class="form-control select2" required><option value="">Cari nama atau nomor telepon</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->nama }} - {{ $customer->no_telp }}</option>@endforeach</select><button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addCustomerModal" aria-label="Tambah pelanggan"><i class="fas fa-plus"></i></button></div></div>
                            <div class="col-md-6"><label class="form-label" for="lapangan_id">Lapangan <span class="text-danger">*</span></label><select name="lapangan_id" id="lapangan_id" class="form-select" required><option value="">Pilih lapangan</option>@foreach($lapangan as $jenis=>$listLapangan)<optgroup label="{{ ucfirst(str_replace('_',' ',$jenis)) }}">@foreach($listLapangan as $lap)<option value="{{ $lap->id }}" data-harga="{{ $lap->harga_per_jam }}">{{ $lap->nama }} - Rp {{ number_format($lap->harga_per_jam,0,',','.') }}/jam</option>@endforeach</optgroup>@endforeach</select></div>
                            <div class="col-md-4"><label class="form-label" for="tanggal_main">Tanggal main <span class="text-danger">*</span></label><input type="date" name="tanggal_main" id="tanggal_main" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('tanggal_main', date('Y-m-d')) }}" required></div>
                            <div class="col-md-4"><label class="form-label" for="jam_mulai">Jam mulai <span class="text-danger">*</span></label><input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="{{ old('jam_mulai') }}" required></div>
                            <div class="col-md-4"><label class="form-label" for="jam_selesai">Jam selesai <span class="text-danger">*</span></label><input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="{{ old('jam_selesai') }}" required></div>
                        </div>
                        <div id="availabilityAlert" class="availability mt-3" role="status" aria-live="polite"></div>
                    </div>
                </section>

                <section class="pos-card">
                    <div class="pos-card-head"><div class="step">2</div><div><h3>Produk tambahan</h3><p>Tambahkan minuman atau perlengkapan ke transaksi.</p></div></div>
                    <div class="pos-card-body"><div id="productList">
                        <div class="product-row product-item"><select name="products[0][product_id]" class="form-select product-select"><option value="">Pilih produk</option>@foreach($products as $product)<option value="{{ $product->id }}" data-harga="{{ $product->harga }}" data-stok="{{ $product->stok }}">{{ $product->nama }} - Rp {{ number_format($product->harga,0,',','.') }} (stok {{ $product->stok }})</option>@endforeach</select><input type="number" name="products[0][qty]" class="form-control product-qty" min="1" placeholder="Qty" aria-label="Jumlah produk"><input type="text" class="form-control product-subtotal" readonly placeholder="Subtotal" aria-label="Subtotal produk"><button type="button" class="btn-icon btn-remove-product" aria-label="Hapus produk"><i class="fas fa-trash-alt"></i></button></div>
                    </div><button type="button" class="btn-add" id="addProduct"><i class="fas fa-plus me-2"></i>Tambah produk</button>
                    <div class="row g-3 mt-3"><div class="col-md-6"><label class="form-label" for="discount_id">Voucher diskon</label><select name="discount_id" id="discount_id" class="form-select"><option value="">Tanpa diskon</option>@foreach($discounts as $diskon)<option value="{{ $diskon->id }}" data-type="{{ $diskon->type }}" data-value="{{ $diskon->value }}">{{ $diskon->kode_voucher }} - {{ $diskon->type == 'percent' ? $diskon->value.'%' : 'Rp '.number_format($diskon->value,0,',','.') }}</option>@endforeach</select></div></div>
                    </div>
                </section>
            </div>
            <div class="col-lg-4"><aside class="summary-card">
                <div class="summary-top"><small>RINGKASAN PEMBAYARAN</small><h3>Pesanan baru</h3></div>
                <div class="summary-body">
                    <div class="summary-line"><span>Lapangan <small id="durationLabel"></small></span><strong id="subtotalLapangan">Rp 0</strong></div>
                    <div class="summary-line"><span>Produk</span><strong id="subtotalProduk">Rp 0</strong></div>
                    <div class="summary-line"><span>Diskon <small id="diskonPersen"></small></span><strong id="potonganHarga">Rp 0</strong></div>
                    <div class="summary-total"><span>Total tagihan</span><strong id="totalBayar">Rp 0</strong></div>
                    <div class="mt-4"><label class="form-label" for="bayar">Jumlah diterima <span class="text-danger">*</span></label><input type="number" name="bayar" id="bayar" class="form-control pay-input" min="0" step="1" placeholder="Masukkan nominal" required></div>
                    <div class="change-box" id="changeBox"><span id="changeLabel">Kembalian</span><strong id="kembalian">Rp 0</strong></div>
                    <div class="mt-4"><label class="form-label d-block">Metode pembayaran <span class="text-danger">*</span></label><div class="pay-methods"><label class="pay-option"><input type="radio" name="payment_method" value="cash" checked><span><i class="fas fa-money-bill-wave"></i> Tunai</span></label><label class="pay-option"><input type="radio" name="payment_method" value="transfer"><span><i class="fas fa-building-columns"></i> Transfer</span></label><label class="pay-option"><input type="radio" name="payment_method" value="qris"><span><i class="fas fa-qrcode"></i> QRIS</span></label></div></div>
                    <button type="submit" class="checkout-btn" id="submitBtn"><i class="fas fa-check-circle me-2"></i>Simpan transaksi</button>
                    <div class="text-center field-hint"><i class="fas fa-lock me-1"></i>Transaksi tersimpan dengan aman</div>
                    <input type="hidden" name="subtotal" id="hiddenSubtotal" value="0"><input type="hidden" name="discount_amount" id="hiddenDiscount" value="0"><input type="hidden" name="total_amount" id="hiddenTotal" value="0">
                </div>
            </aside></div>
        </div>
    </form>
</div>

<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 rounded-4"><div class="modal-header"><div><h5 class="modal-title fw-bold" id="addCustomerTitle">Tambah pelanggan</h5><small class="text-muted">Pelanggan langsung masuk ke transaksi ini.</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="addCustomerForm">@csrf<div class="modal-body"><div class="mb-3"><label class="form-label">Nama</label><input type="text" name="nama" class="form-control" required></div><div class="mb-3"><label class="form-label">Nomor telepon</label><input type="text" name="no_telp" class="form-control" required></div><div><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"></textarea></div><div id="customerError" class="text-danger small mt-3"></div></div><div class="modal-footer border-0"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary" id="saveCustomerBtn">Simpan pelanggan</button></div></form></div></div></div>
@endsection

@push('scripts')
<script>
$(function(){
    $('.select2').select2({width:'100%'});
    let productIndex=1, fieldPrice=0, fieldSubtotal=0, productSubtotal=0, total=0, requestSequence=0, availabilityKnown=false;
    const rupiah=value=>'Rp '+Math.round(value||0).toLocaleString('id-ID');
    const $form=$('#transactionForm'), $submit=$('#submitBtn');
    $submit.prop('disabled',true);

    function calculate(){
        const start=$('#jam_mulai').val(), end=$('#jam_selesai').val();
        fieldSubtotal=0;
        if(start&&end&&fieldPrice){const [sh,sm]=start.split(':').map(Number),[eh,em]=end.split(':').map(Number),hours=(eh*60+em-sh*60-sm)/60;if(hours>0){fieldSubtotal=hours*fieldPrice;$('#durationLabel').text(hours.toLocaleString('id-ID')+' jam')}else $('#durationLabel').text('')}else $('#durationLabel').text('');
        productSubtotal=0;
        $('.product-item').each(function(){const $row=$(this),$option=$row.find('.product-select option:selected'),qty=Number($row.find('.product-qty').val())||0,price=Number($option.data('harga'))||0,stock=Number($option.data('stok'))||0;const rowTotal=qty*price;productSubtotal+=rowTotal;$row.find('.product-subtotal').val(rowTotal?rupiah(rowTotal):'');if(qty>stock&&stock>0){$row.find('.product-qty').addClass('is-invalid').attr('title','Stok tersedia '+stock)}else $row.find('.product-qty').removeClass('is-invalid').removeAttr('title')});
        const subtotal=fieldSubtotal+productSubtotal,$selected=$('#discount_id option:selected'),kind=$selected.data('type'),value=Number($selected.data('value'))||0;let discount=kind==='percent'?subtotal*value/100:(kind==='amount'?Math.min(value,subtotal):0);total=Math.max(0,subtotal-discount);
        $('#subtotalLapangan').text(rupiah(fieldSubtotal));$('#subtotalProduk').text(rupiah(productSubtotal));$('#diskonPersen').text(discount?'('+((discount/subtotal)*100).toFixed(1)+'%)':'');$('#potonganHarga').text(rupiah(discount));$('#totalBayar').text(rupiah(total));
        $('#hiddenSubtotal').val(subtotal.toFixed(2));$('#hiddenDiscount').val(discount.toFixed(2));$('#hiddenTotal').val(total.toFixed(2));calculateChange();
    }
    function calculateChange(){const change=(Number($('#bayar').val())||0)-total,short=change<0;$('#kembalian').text(rupiah(Math.abs(change)));$('#changeLabel').text(short?'Kurang bayar':'Kembalian');$('#changeBox').toggleClass('is-short',short)}
    function checkAvailability(){
        const data={lapangan_id:$('#lapangan_id').val(),tanggal_main:$('#tanggal_main').val(),jam_mulai:$('#jam_mulai').val(),jam_selesai:$('#jam_selesai').val()},$alert=$('#availabilityAlert');
        if(!Object.values(data).every(Boolean)){requestSequence++;$alert.removeClass('alert-success alert-danger alert-warning').empty();availabilityKnown=false;$submit.prop('disabled',true);return}
        const [sh,sm]=data.jam_mulai.split(':').map(Number),[eh,em]=data.jam_selesai.split(':').map(Number),minutes=eh*60+em-sh*60-sm;
        if(minutes<=0||minutes%60!==0){requestSequence++;$alert.removeClass('alert-success alert-warning').addClass('alert-danger').html('<i class="fas fa-circle-exclamation me-2"></i>Pilih durasi booking dalam kelipatan satu jam.');availabilityKnown=false;$submit.prop('disabled',true);return}
        const sequence=++requestSequence;availabilityKnown=false;$submit.prop('disabled',true);$alert.removeClass('alert-success alert-danger').addClass('alert-warning').html('<i class="fas fa-circle-notch fa-spin me-2"></i>Memeriksa jadwal...');
        $.post('{{ route('transactions.check-availability') }}',{...data,_token:'{{ csrf_token() }}'}).done(response=>{if(sequence!==requestSequence)return;availabilityKnown=true;$alert.removeClass('alert-warning alert-success alert-danger').addClass(response.available?'alert-success':'alert-danger').html('<i class="fas '+(response.available?'fa-check-circle':'fa-circle-exclamation')+' me-2"></i>'+response.message);$submit.prop('disabled',!response.available)}).fail(()=>{if(sequence!==requestSequence)return;$alert.removeClass('alert-warning alert-success').addClass('alert-danger').text('Jadwal belum dapat diverifikasi. Coba lagi.');availabilityKnown=false;$submit.prop('disabled',true)})
    }
    $('#lapangan_id').on('change',function(){fieldPrice=Number($(this).find(':selected').data('harga'))||0;calculate();checkAvailability()});$('#tanggal_main,#jam_mulai,#jam_selesai').on('change',function(){calculate();checkAvailability()});$('#discount_id').on('change',calculate);$('#bayar').on('input',calculateChange);$(document).on('change input','.product-select,.product-qty',calculate);
    $('#addProduct').on('click',function(){const $row=$('.product-item:first').clone();$row.find('select').attr('name',`products[${productIndex}][product_id]`).val('');$row.find('.product-qty').attr('name',`products[${productIndex}][qty]`).val('').removeClass('is-invalid');$row.find('.product-subtotal').val('');$('#productList').append($row);productIndex++});$(document).on('click','.btn-remove-product',function(){if($('.product-item').length>1){$(this).closest('.product-item').remove();calculate()}});
    $('#transactionForm').on('submit',function(e){if(!availabilityKnown||$('#availabilityAlert').hasClass('alert-danger')||$('#availabilityAlert').hasClass('alert-warning')){e.preventDefault();return}let stockError=false;$('.product-item').each(function(){const $row=$(this),qty=Number($row.find('.product-qty').val())||0,stock=Number($row.find('.product-select option:selected').data('stok'))||0;if(qty>stock)stockError=true});if(stockError){e.preventDefault();alert('Jumlah produk melebihi stok yang tersedia.');return}if(Number($('#bayar').val())<total){e.preventDefault();$('#bayar').trigger('focus');$('#changeBox').addClass('is-short');return}$submit.prop('disabled',true).html('<i class="fas fa-circle-notch fa-spin me-2"></i>Menyimpan transaksi...')});
    $('#addCustomerForm').on('submit',function(e){e.preventDefault();const $button=$('#saveCustomerBtn');$button.prop('disabled',true).text('Menyimpan...');$('#customerError').empty();$.ajax({url:'{{ route('customers.store') }}',method:'POST',data:$(this).serialize()}).done(response=>{const option=new Option(response.nama+' - '+response.no_telp,response.id,true,true);$('#customer_id').append(option).trigger('change');document.querySelector('#addCustomerModal [data-bs-dismiss="modal"]').click();this.reset()}).fail(xhr=>{$('#customerError').text(xhr.responseJSON?.message||'Pelanggan belum dapat disimpan. Periksa kembali datanya.')}).always(()=>{$button.prop('disabled',false).text('Simpan pelanggan')})});
    calculate();
});
</script>
@endpush
