<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_voucher',
        'type',
        'value',
        'tanggal_mulai',
        'tanggal_selesai',
        'max_penggunaan',
        'digunakan',
        'aktif'
    ];


    public function isValid()
    {
        $now = Carbon::now();
        return $this->aktif
            && $this->kuota > 0
            && $now->between(Carbon::parse($this->mulai_berlaku), Carbon::parse($this->berakhir));
    }

    public function pakai()
    {
        if ($this->kuota > 0) {
            $this->decrement('kuota');
        }
    }
}
