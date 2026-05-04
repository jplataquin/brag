<?php

namespace Database\Seeders;

use App\Models\DiamondPackage;
use Illuminate\Database\Seeder;

class DiamondPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter Pack',
                'diamonds' => 10,
                'price' => 50,
                'currency' => 'PHP',
                'is_active' => true,
                'allow_manual' => true,
                'allow_hitpay' => true,
            ],
            [
                'name' => 'Fighter Pack',
                'diamonds' => 20,
                'price' => 100,
                'currency' => 'PHP',
                'is_active' => true,
                'allow_manual' => true,
                'allow_hitpay' => true,
            ],
            [
                'name' => 'Pro Pack',
                'diamonds' => 50,
                'price' => 120,
                'currency' => 'PHP',
                'is_active' => true,
                'allow_manual' => true,
                'allow_hitpay' => true,
            ],
            [
                'name' => 'Rich Kid Pack',
                'diamonds' => 100,
                'price' => 130,
                'promo_price' => 110,
                'currency' => 'PHP',
                'is_active' => true,
                'allow_manual' => true,
                'allow_hitpay' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            DiamondPackage::updateOrCreate(
                ['name' => $pkg['name']],
                $pkg
            );
        }
    }
}
