<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bento;
use Illuminate\Support\Facades\DB;

class BentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('bento_category')->delete();
        DB::table('bentos')->delete();

        Bento::insert([
            [
                'name' => 'Teriyaki Chicken Bento',
                'description' => 'A delicious bento with teriyaki chicken, rice, and vegetables.',
                'price' => 500,
                'original_price' => 600,
                'usual_discount_percentage' => 20,
                'usual_discounted_price' => 480,
                'estimated_discount_time' => '16:30',
                'discount_status' => 'Check Again',
                'stock_message' => 'Usually runs low by 5 PM',
                'calories' => 450,
                'ingredients' => 'Chicken, Rice, Broccoli, Carrots, Teriyaki Sauce',
                'availability' => 1, // In Stock
                'rating' => 4.5,
                'reviews_count' => 32,
                'store_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beef Yakiniku Bento',
                'description' => 'A flavorful bento with grilled beef, rice, and pickles.',
                'price' => 700,
                'original_price' => 800,
                'usual_discount_percentage' => 15,
                'usual_discounted_price' => 680,
                'estimated_discount_time' => '17:00',
                'discount_status' => 'Active',
                'stock_message' => 'Usually available until 6 PM',
                'calories' => 520,
                'ingredients' => 'Beef, Rice, Pickles, Soy Sauce',
                'availability' => 2, // Limited Stock
                'rating' => 4.2,
                'reviews_count' => 18,
                'store_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
