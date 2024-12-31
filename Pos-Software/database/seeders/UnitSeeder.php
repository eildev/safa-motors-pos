<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'id' => 1,
                'name' => 'Pieces',
            ],
            [
                'id' => 2,
                'name' => 'Dozen',
            ],
            [
                'id' => 3,
                'name' => 'gm',
            ],
            [
                'id' => 4,
                'name' => 'Kg',
            ],
            [
                'id' => 5,
                'name' => 'Litre',
            ],
            [
                'id' => 6,
                'name' => 'Packet',
            ],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
