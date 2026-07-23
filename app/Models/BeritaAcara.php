<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    protected $fillable = [
        'judul',
        'kategori',
        'keterangan',
        'tanggal_kegiatan',
        'gambar_1',
        'gambar_2',
        'gambar_3',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kegiatan' => 'date',
        ];
    }
}
