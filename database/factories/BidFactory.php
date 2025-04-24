<?php

namespace Database\Factories;

use App\Models\Lawyer;
use App\Models\Lawsuit;
use App\Models\Bid;
use Illuminate\Database\Eloquent\Factories\Factory;

class BidFactory extends Factory
{
    protected $model = Bid::class;

    public function definition()
    {
        return [
            'case_id' => Lawsuit::inRandomOrder()->first()->id, // Random case
            'lawyer_id' => Lawyer::inRandomOrder()->first()->id, // Random lawyer
            'fee' => $this->faker->randomFloat(2, 1000, 5000), // Random fee between 1000 and 5000
            'time_estimated' => $this->faker->date(), // Random estimated time
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']), // Random status
        ];
    }
}
