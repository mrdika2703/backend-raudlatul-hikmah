<?php

namespace Database\Seeders;

use App\Models\BeritaAcara;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeritaAcaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BeritaAcara::insert([
            [
                'judul' => 'Peringatan Hari Anak Nasional & Lomba Mewarnai',
                'kategori' => 'Acara Sekolah',
                'keterangan' => 'Acara berlangsung meriah diikuti oleh seluruh siswa Kelas A dan Kelas B beserta wali murid.',
                'tanggal_kegiatan' => now()->subDays(10)->toDateString(),
                'gambar_1' => null,
                'gambar_2' => null,
                'gambar_3' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Rapat Pembagian Rapor Semester Ganjil',
                'kategori' => 'Akademik',
                'keterangan' => 'Pertemuan antara pihak sekolah dan wali murid untuk menyampaikan perkembangan belajar anak.',
                'tanggal_kegiatan' => now()->subDays(2)->toDateString(),
                'gambar_1' => null,
                'gambar_2' => null,
                'gambar_3' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
