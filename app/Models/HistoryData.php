<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryData extends Model
{
    protected $table = 'history_data';

    protected $fillable = [
        'user_id',
        'category',
        'keterangan',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
