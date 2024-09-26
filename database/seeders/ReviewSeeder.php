<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        // Make sure users exist for the foreign key (user_id)
        $users = DB::table('users')->pluck('id')->toArray();
        
        if (count($users) == 0) {
            $this->command->info('No users found, make sure users are seeded first.');
            return;
        }

        // Sample data for reviews
        $reviews = [
            [
                'user_id' => $users[array_rand($users)], // Random user ID from the users table
                'bento_id' => 2,
                'comment' => 'This bento is amazing! Perfect portion size.',
                'rating' => 4.5,
                'review_text' => 'I loved the chicken bento, especially the teriyaki sauce. Will order again!',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $users[array_rand($users)],
                'bento_id' => 3,
                'comment' => 'A bit too salty for my taste.',
                'rating' => 3.0,
                'review_text' => 'The beef bento was okay, but I found it a bit too salty. Otherwise, great quality.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
        ];

        // Insert the sample reviews into the database
        DB::table('reviews')->insert($reviews);
    }
}
