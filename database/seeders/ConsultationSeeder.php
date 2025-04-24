<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consultation;

class ConsultationSeeder extends Seeder
{
    public function run()
    {
        Consultation::factory(10)->create(); // Generate 10 consultations
    }
}
