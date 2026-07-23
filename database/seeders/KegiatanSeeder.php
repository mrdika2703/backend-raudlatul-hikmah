<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kegiatan::insert([
            [
                'judul' => 'Drumband',
                'kategori' => 'Ekstrakurikuler',
                'keterangan' => 'Kegiatan rutin setiap pagi untuk melatih keseimbangan dan kesehatan fisik anak.',
                'icon' => 'Activity', // Lucide icon
                'gambar_1' => null,
                'gambar_2' => null,
                'gambar_3' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Berenang',
                'kategori' => 'Ekstrakurikuler',
                'keterangan' => 'Belajar di luar kelas mengenal alam dan profesi di lingkungan sekitar.',
                'icon' => 'Compass', // Lucide icon
                'gambar_1' => null,
                'gambar_2' => null,
                'gambar_3' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
