<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::Create([
            'name' => 'Mohammad Syamsudin',
            'email' => 'syamsudin@gmail.com',
            'password' => bcrypt('rahasia123'),
            'position' => 'Guru',
            'category' => 'Admin',
            'photo' => null,
            'address' => 'Jl. Contoh No. 1',
            'description' => null,
            'created_at' => now(),
        ]);
        User::Create([
            'name' => 'Siti Rosyidah',
            'email' => 'Rosyidah@gmail.com',
            'password' => bcrypt('rahasia123'),
            'position' => 'Operator',
            'category' => 'Operator dan guru',
            'photo' => null,
            'address' => 'Jl. Contoh No. 1',
            'description' => null,
            'created_at' => now(),
        ]);
        User::Create([
            'name' => 'Khur',
            'email' => 'khur@gmail.com',
            'password' => bcrypt('rahasia123'),
            'position' => 'Kepala Sekolah',
            'category' => 'Kepala Sekolah TK',
            'photo' => null,
            'address' => 'Jl. Contoh No. 1',
            'description' => null,
            'created_at' => now(),
        ]);
    }
}
