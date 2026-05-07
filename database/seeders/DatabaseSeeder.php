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
        $this->call([
            KategoriMenuSeeder::class
        ]);

        User::create([
            'name' => 'Admin',
            'password' => bcrypt('12345'),
            'email' => 'admin@gmail.com',
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'kasir 1',
            'password' => bcrypt('12345'),
            'email' => 'kasir1@gmail.com',
            'role' => 'kasir'
        ]);

        User::create([
            'name' => 'kasir',
            'password' => bcrypt('12345'),
            'email' => 'kasir@gmail.com',
            'role' => 'kasir'
        ]);
    }
}
