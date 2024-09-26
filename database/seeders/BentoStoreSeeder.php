<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bento;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class BentoStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all bentos and stores to create relationships
        $bentos = Bento::all();
        $stores = Store::all();

        // Check if there are bentos and stores to associate
        if ($bentos->isEmpty() || $stores->isEmpty()) {
            $this->command->info('No bentos or stores found. Make sure to seed bentos and stores first.');
            return;
        }

        // Create sample relationships between bentos and stores
        foreach ($bentos as $bento) {
            // Select random stores for each bento
            $selectedStores = $stores->random(rand(1, 3)); // Randomly select between 1 to 3 stores

            foreach ($selectedStores as $store) {
                DB::table('bento_store')->insert([
                    'bento_id' => $bento->id,
                    'store_id' => $store->id,
                    'current_discount' => rand(0, 50), // Random discount between 0% and 50%
                    'stock_level' => rand(1, 100), // Random stock level between 1 and 100
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('BentoStoreSeeder seeded successfully.');
    }
}
