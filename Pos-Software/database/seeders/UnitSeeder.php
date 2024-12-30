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
            'related_to_unit' => 'pc',
            'related_sign' => '*',
            'related_by' => 12,
            ],
            [
                'id' => 3,
                'name' => 'gm',
            ],
            [
                'id' => 4,
                'name' => 'Kg',
                'related_to_unit' => 'gm',
                'related_sign' => '*',
                'related_by' => 1000,
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
            unit::create($unit);
        }
    }
}
