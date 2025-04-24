<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Lawyer;
use App\Models\Lawsuit;
use App\Models\Consultation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultationFactory extends Factory
{
    protected $model = Consultation::class;

    public function definition()
    {
        $client = Client::inRandomOrder()->first() ?? Client::factory()->create();
        $lawyer = Lawyer::inRandomOrder()->first() ?? Lawyer::factory()->create();
        $case = Lawsuit::inRandomOrder()->first() ?? Lawsuit::factory()->create([
            'client_id' => $client->id,
        ]);

        return [
            'client_id' => $client->id,
            'lawyer_id' => $lawyer->id,
            'case_id' => $case->id,
            'date_and_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'mode' => $this->faker->randomElement(['virtual', 'physical']),
            'status' => $this->faker->randomElement(['Scheduled', 'Completed', 'Missed']),
        ];
    }
}
