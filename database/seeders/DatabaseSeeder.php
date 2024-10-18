<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        for($i = 0; $i < 10;$i++){
            User::factory()->create([
                'name' => 'Test User'.$i,
                'email' => 'test'.($i+3).'@example.com',
                'password' => $password ??= Hash::make('password'.$i),
                'deaths' => fake()->numberBetween(1, 10),
                'kills' => fake()->numberBetween(1, 10),
                'points' => fake()->numberBetween(1, 10),
                'boss1lvl' => fake()->numberBetween(1, 10),
                'boss2lvl' => fake()->numberBetween(1, 10),
                'boss3lvl' => fake()->numberBetween(1, 10),
            ]);
        }
        
    }
}
