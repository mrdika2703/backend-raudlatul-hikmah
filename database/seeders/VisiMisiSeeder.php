<?php

namespace Database\Seeders;

use App\Models\VisiMisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisiMisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VisiMisi::insert([
            [
                'kategori' => 'Visi',
                'keterangan' => 'Mewujudkan generasi usia dini yang berakhlak mulia, kreatif, mandiri, dan berprestasi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Misi',
                'keterangan' => 'Menyelenggarakan pembelajaran yang aktif, kreatif, efektif, dan menyenangkan (PAKEM).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Misi',
                'keterangan' => 'Membiasakan perilaku santun, agamis, dan mandiri dalam kehidupan sehari-hari di sekolah.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
