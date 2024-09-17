<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Bento;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        // Ensure we have users and bentos to associate with reviews
        $users = User::all();
        $bentos = Bento::all();

        if ($users->isEmpty() || $bentos->isEmpty()) {
            $this->command->info('Please seed users and bentos tables first.');
            return;
        }

        foreach ($bentos as $bento) {
            $numberOfReviews = rand(1, 5);  // Random number of reviews per bento

            for ($i = 0; $i < $numberOfReviews; $i++) {
                Review::create([
                    'user_id' => $users->random()->id,
                    'bento_id' => $bento->id,
                    'comment' => $this->getRandomComment(),
                    'rating' => rand(1, 5),
                ]);
            }
        }
    }

    private function getRandomComment()
    {
        $comments = [
            "Delicious! Will order again.",
            "Good value for money.",
            "Fresh ingredients, loved it!",
            "Portion size could be bigger.",
            "Great taste, but delivery was slow.",
            "Perfect for a quick lunch.",
            "Healthy and tasty option.",
            "Not bad, but I've had better.",
            "Exceeded my expectations!",
            "Will recommend to friends.",
        ];

        return $comments[array_rand($comments)];
    }
}
