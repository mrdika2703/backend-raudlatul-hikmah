<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Fasilitas::insert([
            [
                'judul' => 'Kamar mandi',
                'gambar' => null,
                'created_at' => now(),
            ],
            [
                'judul' => 'Ruang Kelas',
                'gambar' => null,
                'created_at' => now(),
            ],
            [
                'judul' => 'Taman Bermain',
                'gambar' => null,
                'created_at' => now(),
            ],
            [
                'judul' => 'Musholla',
                'gambar' => null,
                'created_at' => now(),
            ],
        ]);
    }
}
