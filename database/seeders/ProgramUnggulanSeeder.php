<?php

namespace Database\Seeders;

use App\Models\ProgramUnggulan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramUnggulanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProgramUnggulan::insert([
            [
                'icon' => 'BookOpen', // Lucide icon
                'judul' => 'Tahfidz & Baca Al-Qur\'an',
                'keterangan' => 'Membiasakan anak menghafal surat-surat pendek dan doa harian dengan metode yang menyenangkan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'Award', // Lucide icon
                'judul' => 'Pengembangan Karakter & Moral',
                'keterangan' => 'Fokus pada pembentukan akhlak mulia, kemandirian, dan etika sejak usia dini.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'Palette', // Lucide icon
                'judul' => 'Kelas Kreativitas & Seni',
                'keterangan' => 'Mengembangkan motorik halus anak melalui kegiatan menggambar, mewarnai, dan kerajinan tangan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
