<?php

namespace Database\Seeders;

use App\Models\Camp;
use Illuminate\Database\Seeder;

class CampSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'title' => 'Gila Belajar',
                'slug' => 'gila-belajar',
                'price' => 280000,
                'benefits' => [
                    'Pro Techstack Kit',
                    'iMac Pro 2021 & Display',
                    '1-1 Mentoring Program',
                    'Final Project Certificate',
                    'Offline Course Videos',
                    'Future Job Opportunity',
                    'Premium Design Kit',
                    'Website Builder',
                ],
            ],
            [
                'title' => 'Baru Mulai',
                'slug' => 'baru-mulai',
                'price' => 140000,
                'benefits' => [
                    '1-1 Mentoring Program',
                    'Final Project Certificate',
                    'Offline Course Videos',
                    'Future Job Opportunity',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            $camp = Camp::create([
                'title' => $plan['title'],
                'slug' => $plan['slug'],
                'price' => $plan['price'],
            ]);

            $camp->benefits()->createMany(
                array_map(fn ($name) => ['name' => $name], $plan['benefits'])
            );
        }
    }
}