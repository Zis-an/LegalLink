<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bid;

class BidSeeder extends Seeder
{
    public function run()
    {
        Bid::factory(10)->create(); // Generate 10 bids
    }
}
