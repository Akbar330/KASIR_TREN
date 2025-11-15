<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi - {{ $transaction->kode_transaksi }}</title>
    <style>
        @media print {
            @page {
                margin: 0;
            }

            body {
                margin: 1cm;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }

        .header p {
            margin: 3px 0;
            font-size: 12px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 12px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }

        table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }

        table td {
            padding: 5px 0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            border-top: 2px solid #000;
            margin-top: 10px;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 11px;
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-print:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <button class="btn-print no-print" onclick="window.print()">🖨️ Print Struk</button>

    <div class="header">
        <h2>FUTSAL BOOKING</h2>
        <p>Jl. Contoh No. 123, Sumedang</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="info-row">
        <span>Kode</span>
        <span><strong>{{ $transaction->kode_transaksi }}</strong></span>
    </div>

    <div class="info-row">
        <span>Tanggal</span>
        <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
    </div>

    <div class="info-row">
        <span>Kasir</span>
        <span>{{ $transaction->user->name }}</span>
    </div>

    <div class="info-row">
        <span>Customer</span>
        <span>{{ $transaction->customer->nama }}</span>
    </div>

    <div class="divider"></div>

    <div style="font-size: 13px; font-weight: bold; margin: 10px 0;">DETAIL BOOKING</div>

    <div class="info-row">
        <span>Lapangan</span>
        <span>{{ $transaction->lapangan->nama }}</span>
    </div>

    <div class="info-row">
        <span>Tanggal Main</span>
        <span>{{ \Carbon\Carbon::parse($transaction->tanggal_main)->format('d/m/Y') }}</span>
    </div>

    <div class="info-row">
        <span>Waktu</span>
        <span>{{ substr($transaction->jam_mulai, 0, 5) }} - {{ substr($transaction->jam_selesai, 0, 5) }}</span>
    </div>

    <div class="info-row">
        <span>Durasi</span>
        <span>{{ $transaction->durasi_jam }} Jam</span>
    </div>

    <div class="info-row">
        <span>Harga</span>
        <span>Rp {{ number_format($transaction->subtotal_lapangan, 0, ',', '.') }}</span>
    </div>

    @if ($transaction->details->count() > 0)
        <div class="divider"></div>

        <div style="font-size: 13px; font-weight: bold; margin: 10px 0;">PRODUK</div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->details as $detail)
                    <tr>
                        <td>{{ $detail->product->nama }}</td>
                        <td class="text-center">{{ $detail->qty }}</td>
                        <td class="text-right">{{ number_format($detail->harga, 0) }}</td>
                        <td class="text-right">{{ number_format($detail->subtotal, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="total-section">
        <div class="info-row">
            <span>Subtotal Lapangan</span>
            <span><strong>Rp {{ number_format($transaction->subtotal_lapangan, 0, ',', '.') }}</strong></span>
        </div>

        <div class="info-row">
            <span>Subtotal Produk</span>
            <span><strong>Rp {{ number_format($transaction->subtotal_produk, 0, ',', '.') }}</strong></span>
        </div>

        {{-- 🧾 Tambahan: Diskon --}}
        @if ($transaction->discount_id && $transaction->discount_amount > 0)
            <div class="divider"></div>
            <div class="info-row" style="font-weight: bold; color: #28a745;">
                <span>💰 DISKON DIGUNAKAN</span>
            </div>
            <div class="info-row">
                <span>Kode Voucher</span>
                <span><strong>{{ $transaction->discount->kode_voucher }}</strong></span>
            </div>
            <div class="info-row">
                <span>Jenis Diskon</span>
                <span>
                    @if ($transaction->discount->type == 'percent')
                        Persentase ({{ $transaction->discount->value }}%)
                    @else
                        Nominal (Rp {{ number_format($transaction->discount->value, 0, ',', '.') }})
                    @endif
                </span>
            </div>
            <div class="info-row" style="color: #dc3545;">
                <span>Potongan Harga</span>
                <span><strong>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</strong></span>
            </div>
        @endif

        <div class="divider"></div>

        <div class="info-row" style="font-size: 14px;">
            <span><strong>TOTAL</strong></span>
            <span><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></span>
        </div>

        <div class="info-row">
            <span>Bayar</span>
            <span>Rp {{ number_format($transaction->bayar, 0, ',', '.') }}</span>
        </div>

        <div class="info-row">
            <span>Kembalian</span>
            <span>Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</span>
        </div>

        <div class="info-row">
            <span>Metode</span>
            <span>{{ strtoupper($transaction->payment_method) }}</span>
        </div>
    </div>

    <div class="footer">
        <p><strong>TERIMA KASIH</strong></p>
        <p>Harap datang 10 menit sebelum jadwal</p>
        <p>Simpan struk ini sebagai bukti booking</p>
        <p style="margin-top: 10px;">www.futsalbooking.com</p>
    </div>

</body>

</html>
