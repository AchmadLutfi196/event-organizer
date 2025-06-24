<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat admin user
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'is_active' => true
        ]);

        // Buat beberapa user biasa
        User::factory()->create([
            'name' => 'User 1',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_active' => true
        ]);

        // Seed kategoris
        $this->call(KategoriSeeder::class);

        // Buat sample barang
        \App\Models\Barang::factory(20)->create();
    }
}