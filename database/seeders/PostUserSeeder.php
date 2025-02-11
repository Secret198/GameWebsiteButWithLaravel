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

        $users = User::all();
        $seedNum = count($users);

        $bar = progress("Seeding Achievement User connect", $seedNum);
        
        foreach($users as $user){
            $user->checkForAchievements();
            $bar->advance();
        }

        $bar->finish();
    }
}
