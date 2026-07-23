<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'siswa_id',
        'absen_date',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'absen_date' => 'datetime',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
