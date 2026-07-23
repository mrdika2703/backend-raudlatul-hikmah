<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $fillable = [
        'judul',
        'kategori',
        'keterangan',
        'icon',
        'gambar_1',
        'gambar_2',
        'gambar_3',
    ];
}
