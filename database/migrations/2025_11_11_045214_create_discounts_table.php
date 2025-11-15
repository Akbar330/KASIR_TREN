<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('kode_voucher')->unique(); // contoh: KEMERDEKAAN17
            $table->enum('type', ['percent', 'amount']); // jenis diskon
            $table->decimal('value', 10, 2); // nilai diskon (kalau percent = persen, kalau amount = nominal)
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->integer('max_penggunaan')->default(1); // berapa kali voucher bisa dipakai
            $table->integer('digunakan')->default(0); // counter berapa kali udah dipakai
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
