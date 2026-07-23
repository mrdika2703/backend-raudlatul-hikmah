<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Siswa::create([
            'nisn' => '123456789',
            'nama_lengkap' => 'Ahmad',
            'jenis_kelamin' => 'Laki-laki',
            'kelas_id' => 1,
        ]);

        Siswa::create([
            'nisn' => '123456711',
            'nama_lengkap' => 'Susanto',
            'jenis_kelamin' => 'Laki-laki',
            'kelas_id' => 1,
        ]);

        Siswa::create([
            'nisn' => '123456712',
            'nama_lengkap' => 'Sinta',
            'jenis_kelamin' => 'Perempuan',
            'kelas_id' => 2,
        ]);
    }
}
