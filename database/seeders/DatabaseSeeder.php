<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun admin default
        User::firstOrCreate(
            ['email' => 'pusdatin.magang@pu.go.id'],
            [
                'name' => 'PUSDATIN Magang',
                'password' => bcrypt('kepegpusdatin05&'),
            ]
        );

        $this->call([
            TimKerjaSeeder::class,
            PesertaMagangSeeder::class,
        ]);
    }
}
