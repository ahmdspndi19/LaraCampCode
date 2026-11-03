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
                'description' => 'Paket lengkap: belajar dari dasar sampai membangun proyek nyata, lengkap dengan perangkat kerja, mentoring 1-1, dan peluang karier.',
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
                'description' => 'Paket dasar untuk kamu yang baru mulai: video kursus, mentoring 1-1, dan sertifikat proyek akhir.',
                'price' => 140000,
                'benefits' => [
                    '1-1 Mentoring Program',
                    'Final Project Certificate',
                    'Offline Course Videos',
                    'Future Job Opportunity',
                ],
            ],
        ];

        // Idempotent: aman dijalankan berulang tanpa menggandakan data.
        foreach ($plans as $plan) {
            $camp = Camp::withTrashed()->updateOrCreate(
                ['slug' => $plan['slug']],
                [
                    'title' => $plan['title'],
                    'description' => $plan['description'],
                    'price' => $plan['price'],
                ]
            );

            if ($camp->trashed()) {
                $camp->restore();
            }

            $camp->benefits()->delete();
            $camp->benefits()->createMany(
                array_map(fn ($name) => ['name' => $name], $plan['benefits'])
            );
        }
    }
}
