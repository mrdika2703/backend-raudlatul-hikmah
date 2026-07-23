<?php

namespace Database\Seeders;

use App\Models\Rapor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RaporSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rapor::insert([
            [
                'siswa_id' => 1,
                'tanggal' => now()->toDateString(),
                'kegiatan' => 'Anak sangat aktif saat sesi tanya jawab dan mampu menyelesaikan tugas mewarnai dengan rapi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'siswa_id' => 2,
                'tanggal' => now()->toDateString(),
                'kegiatan' => 'Anak mengikuti kegiatan senam pagi dengan antusias namun perlu pendampingan saat fokus menulis.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'siswa_id' => 3,
                'tanggal' => now()->toDateString(),
                'kegiatan' => 'Siswa izin tidak masuk sekolah karena acara keluarga.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
