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
        $achievementData = [
            "First Blood",
            "Halj meg az első alkalommal",
            "deaths",
            1,
            "Death's Embrace",
            "Halj meg 10-szer",
            "deaths",
            10,
            "Reaper's Favourite",
            "Halj meg 100-szor",
            "deaths",
            100,
            "Enemy Slaughterer",
            "Ölj meg 100 ellenséget",
            "kills",
            100,
            "Massacre Master",
            "Ölj meg 1000 ellenséget",
            "kills",
            1000,
            "The Endless Hunter",
            "Ölj meg 10000 ellenséget",
            "kills",
            10000,
            "First Victim",
            "Győzd le az 1. boss-t",
            "boss1lvl",
            1,
            "Second Striker",
            "Győzd le az 2. boss-t",
            "boss2lvl",
            1,
            "3rd Time's the Charm",
            "Győzd le az 3. boss-t",
            "boss3lvl",
            1,
            "Wave Surfer",
            "Érd el az 5. wave-et",
            "waves",
            5,
            "Wave Crusher",
            "Érd el az 10. wave-et",
            "waves",
            10,
            "Wave Slayer",
            "Érd el az 20. wave-et",
            "waves",
            20,

        ];

        $seedNum = count($achievementData) / 4;
        $bar = progress("Seeding achievements", $seedNum);
        for($i = 0; $i < count($achievementData); $i+=4){
            
            $achievement = new Achievement([
                "name" => $achievementData[$i],
                "description" => $achievementData[$i+1],
                "field" => $achievementData[$i+2],
                "threshold" => $achievementData[$i+3],
            ]);
            $achievement->save();
            $bar->advance();
        }
        $bar->finish();
    }
}
