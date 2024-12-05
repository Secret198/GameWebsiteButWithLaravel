<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use function Laravel\Prompts\progress;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedNum = 100;
        $users = User::select('id')->get()->toArray();
        $postBar = progress("Seeding posts", $seedNum);
        for( $i = 0; $i < $seedNum; $i++ ){
            // $post = new Post(["post" => fake()->realText(50), "likes" => fake()->numberBetween(0, 3000), "image" => fake()->realText(10).".png", "user_id" => fake()->randomElement($users)['id']]);
            $post = new Post(["post" => fake()->realText(50), "likes" => fake()->numberBetween(0, 3000), "user_id" => fake()->randomElement($users)['id']]);
            $post->save();
            $postBar->advance();
        }
        $postBar->finish();
    }
}
