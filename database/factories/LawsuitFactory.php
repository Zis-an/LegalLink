<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Lawsuit;
use Illuminate\Database\Eloquent\Factories\Factory;

class LawsuitFactory extends Factory
{
    protected $model = Lawsuit::class;

    public function definition()
    {
        return [
            'client_id' => Client::inRandomOrder()->first()->id, // Random client
            'title' => $this->faker->title, // Random title
            'category' => $this->faker->randomElement(['Civil', 'Criminal']), // Random category
            'subcategory' => $this->faker->randomElement(['Property Disputes', 'Contract Disputes', 'Crimes Against Persons']), // Random subcategory
            'description' => $this->faker->paragraph, // Random description
            'voice_note' => 'audio/flute.mp3', // Random voice note filename
            'status' => $this->faker->randomElement(['open', 'in_progress', 'closed']), // Random status
        ];
    }
}
