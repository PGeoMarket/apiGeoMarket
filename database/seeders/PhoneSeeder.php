<?php
// PhoneSeeder.php
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
            // Cada seller tiene entre 1-2 teléfonos
            $phoneCount = rand(1, 2);
            
            for ($i = 0; $i < $phoneCount; $i++) {
                Phone::factory()->create([
                    'seller_id' => $seller->id,
                ]);
            }
        }
    }
}