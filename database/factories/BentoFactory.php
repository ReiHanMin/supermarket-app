<?php

namespace Database\Factories;

use App\Models\Bento;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Store; // Assuming Store model is used here

class BentoFactory extends Factory
{
    protected $model = Bento::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word, // Add this line if it's missing
            'store_id' => Store::factory(),
            // other fields...
        ];
    }
}
