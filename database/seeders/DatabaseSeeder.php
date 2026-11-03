<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CampSeeder::class);

        // is_admin tidak mass-assignable, jadi diisi lewat forceFill().
        // Password di bawah hanya untuk development. Ganti sebelum deploy.
        $admin = User::firstOrNew(['email' => 'admin@campcode.test']);
        $admin->forceFill([
            'name' => 'Admin',
            'password' => 'password',
            'is_admin' => true,
        ])->save();

        $user = User::firstOrNew(['email' => 'user@campcode.test']);
        $user->forceFill([
            'name' => 'Beatrice',
            'password' => 'password',
            'occupation' => 'Product Manager',
        ])->save();
    }
}
