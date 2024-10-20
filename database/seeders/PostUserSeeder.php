<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use function Laravel\Prompts\progress;

class PostUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedNum = 10;

        $users = User::select('id')->get()->toArray();
        $achievements = Achievement::select('id')->get()->toArray();
        $bar = progress("Seeding Post User connect", $seedNum);
        
        for( $i = 0; $i < $seedNum; $i++ ){   
            DB::table('user_achievement')->insert([
                'user_id' => fake()->randomElement($users),
                'achievement_id' => fake()->randomElement($achievements),
            ]);
            $bar->advance();
        }

        $bar->finish();
    }
}
