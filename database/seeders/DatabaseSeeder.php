<?php

namespace Database\Seeders;

use App\Models\Category;
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
        collect([
            ['name' => 'Travel', 'color' => '#2563eb', 'icon' => 'airplane'],
            ['name' => 'Food', 'color' => '#f97316', 'icon' => 'cup-hot'],
            ['name' => 'Stay', 'color' => '#14b8a6', 'icon' => 'house-heart'],
            ['name' => 'Shopping', 'color' => '#db2777', 'icon' => 'bag'],
            ['name' => 'Utilities', 'color' => '#64748b', 'icon' => 'lightning-charge'],
            ['name' => 'Events', 'color' => '#7c3aed', 'icon' => 'calendar-event'],
        ])->each(fn ($category) => Category::firstOrCreate(['name' => $category['name']], $category));

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
