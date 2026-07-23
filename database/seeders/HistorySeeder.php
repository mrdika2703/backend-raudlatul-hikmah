<?php

namespace Database\Seeders;

use App\Models\HistoryData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HistoryData::insert([
            [
                'user_id' => 1,
                'category' => 'Absen',
                'keterangan' => 'Mohammad Syamsudin melakukan absensi masuk harian siswa kelas A.',
                'date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'category' => 'Tambah',
                'keterangan' => 'Siti Rosyidah menambahkan data kegiatan baru: Kunjungan Edukasi.',
                'date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'category' => 'Edit',
                'keterangan' => 'Khur memperbarui teks Visi dan Misi sekolah.',
                'date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
