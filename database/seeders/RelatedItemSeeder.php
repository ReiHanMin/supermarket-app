<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bento;
use App\Models\RelatedItem;

class RelatedItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Fetch all bentos to generate related items
        $bentos = Bento::all();

        // Check if there are any bentos to work with
        if ($bentos->isEmpty()) {
            $this->command->info('No bentos found. Please seed the bentos table first.');
            return;
        }

        // Create related items for each bento
        foreach ($bentos as $bento) {
            // Example: Create 2 related items for each bento
            for ($i = 0; $i < 2; $i++) {
                RelatedItem::create([
                    'bento_id' => $bento->id,
                    'related_item_name' => $bento->name . ' - Related Item ' . ($i + 1),
                    'original_price' => $bento->original_price + rand(100, 500), // Slightly different price
                    'discounted_price' => $bento->original_price + rand(50, 300), // Discounted price
                    'similarity_score' => rand(70, 100) / 100, // Random similarity score between 0.7 and 1.0
                ]);
            }
        }

        $this->command->info('RelatedItemSeeder: Seeded related items for bentos.');
    }
}
