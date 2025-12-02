<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lapangan;
class KategoriLapang extends Model
{
    protected $table = 'kategori_lapangs';

    protected $fillable = [
        'nama'
    ];

    public function lapangans()
    {
        return $this->hasMany(Lapangan::class, 'kategori_lapangs_id', 'id');
    }
}
