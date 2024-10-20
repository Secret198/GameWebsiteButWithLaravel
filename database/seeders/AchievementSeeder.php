<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;
use function Laravel\Prompts\progress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedNum = 10;
        $bar = progress("Seeding achievements", $seedNum);
        for($i = 0; $i < $seedNum; $i++){
            $achievement = new Achievement(['name' => fake()->realText(10)]);
            $achievement->save();
            $bar->advance();
        }
        $bar->finish();
    }
}
