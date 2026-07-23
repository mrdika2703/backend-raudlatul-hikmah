<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membungkus semua data ke dalam satu array multidimensi tunggal
        Kelas::insert([
            [
                'kelas' => 'A',
                'semester' => 'Ganjil',
                'tahun_ajaran' => '2022/2023',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas' => 'B',
                'semester' => 'Ganjil',
                'tahun_ajaran' => '2022/2023',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas' => 'A',
                'semester' => 'Ganjil',
                'tahun_ajaran' => '2023/2024',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas' => 'B',
                'semester' => 'Ganjil',
                'tahun_ajaran' => '2023/2024',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
