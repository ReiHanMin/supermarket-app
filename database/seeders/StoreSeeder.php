<?php

namespace Database\Seeders;

use App\Models\Store; // Assuming you have a Store model
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create some sample stores
        $stores = [
            [
                'name' => 'Kyoto Supermarket',
                'address' => '123 Kyoto Street, Kyoto, Japan',
                'phone' => '075-123-4567',
                'email' => 'contact@kyotosupermarket.jp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Osaka Grocery',
                'address' => '456 Osaka Road, Osaka, Japan',
                'phone' => '06-789-0123',
                'email' => 'info@osakagrocery.jp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tokyo Mart',
                'address' => '789 Tokyo Avenue, Tokyo, Japan',
                'phone' => '03-456-7890',
                'email' => 'support@tokyomart.jp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more stores as needed
        ];

        // Insert the stores into the database
        DB::table('stores')->insert($stores);
    }
}
