<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lawsuit;

class LawsuitSeeder extends Seeder
{
    public function run()
    {
        Lawsuit::factory(10)->create(); // Generate 10 lawsuits
    }
}
