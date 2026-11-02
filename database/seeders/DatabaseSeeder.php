<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CampSeeder::class);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@campcode.test',
            'password' => 'password',
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Beatrice',
            'email' => 'user@campcode.test',
            'password' => 'password',
            'occupation' => 'Product Manager',
        ]);
    }
}