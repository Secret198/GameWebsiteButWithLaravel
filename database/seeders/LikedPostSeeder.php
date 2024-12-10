<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use DB;
use function Laravel\Prompts\progress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LikedPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedNum = 10;

        $users = User::select('id')->get()->toArray();
        $posts = Post::select('id')->get()->toArray();
        $bar = progress("Seeding liked posts connect", $seedNum);
        
        for( $i = 0; $i < $seedNum; $i++ ){   
            DB::table('post_user')->insert([
                'user_id' => fake()->randomElement($users)['id'],
                'post_id' => fake()->randomElement($posts)['id'],
            ]);
            $bar->advance();
        }

        $bar->finish();
    }
}
