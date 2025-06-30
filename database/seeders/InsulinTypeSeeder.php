<?php

namespace Database\Seeders;

use App\Models\InsulinType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InsulinTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insulinTypes = [
            [
                'name' => 'Novorapid',
                'is_rapid' => true,
                'description' => 'Fast-acting insulin for controlling blood sugar during meals and for high blood sugar corrections.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Lantus',
                'is_rapid' => false,
                'description' => 'Long-acting basal insulin that provides consistent background insulin for 24 hours.',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($insulinTypes as $insulinType) {
            InsulinType::updateOrCreate(
                ['name' => $insulinType['name']],
                $insulinType
            );
        }
    }
}
