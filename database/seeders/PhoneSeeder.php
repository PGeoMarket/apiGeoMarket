<?php

namespace Database\Seeders;

use App\Models\Phone;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class PhoneSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = Seller::all();
        foreach ($sellers as $seller) {
            Phone::factory(rand(1, 2))->create([
                'seller_id' => $seller->id,
            ]);
        }
    }
}
