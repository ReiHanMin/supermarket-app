<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bento;
use App\Models\Store;

class BentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            Bento::factory()->create([
                'name' => 'Some Bento Name', // Add this line
                'price' => 10.99, // Add a price value
                'store_id' => $store->id // Assuming you have a factory for Bento
            ]);
        }
    }
}
