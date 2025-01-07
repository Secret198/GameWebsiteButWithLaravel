<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\progress;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seedNum = 10;
        $userBar = progress("Seeding Users", $seedNum);
        for($i = 0; $i < 10;$i++){
            User::factory()->create([
                'name' => 'Test User'.$i,
                'email' => 'test'.($i+3).'@example.com',
                'password' => 'password'.$i,
                'deaths' => fake()->numberBetween(1, 10),
                'kills' => fake()->numberBetween(1, 10),
                'waves' => fake()->numberBetween(1, 10),
                'boss1lvl' => fake()->numberBetween(0, 1),
                'boss2lvl' => fake()->numberBetween(0, 1),
                'boss3lvl' => fake()->numberBetween(0, 1),
                'privilege' => fake()->randomElement([1, 10]),
            ]);
            $userBar->advance();
        }
        $userBar->finish();

        $this->call([
            PostSeeder::class,
            AchievementSeeder::class,
            PostUserSeeder::class,
        ]);
        
    }
}
