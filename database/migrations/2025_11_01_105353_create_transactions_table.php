<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // kasir
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('lapangan_id')->constrained('lapangan')->onDelete('cascade');
            $table->date('tanggal_main');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('durasi_jam');
            $table->decimal('subtotal_lapangan', 10, 2);
            $table->decimal('subtotal_produk', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->decimal('bayar', 10, 2);
            $table->decimal('kembalian', 10, 2);
            $table->enum('status_booking', ['pending','selesai','dibatalkan','pending_cancel']);
            $table->enum('payment_method', ['cash', 'transfer', 'qris']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
