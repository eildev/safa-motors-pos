<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'id' => 1,
                'name' => 'Local Brand',
                'slug' => 'local-brand',
            ],
            [
                'id' => 2,
                'name' => 'Basundhara',
                'slug' => 'basundhara',
            ],
            [
                'id' => 3,
                'name' => 'Fresh',
                'slug' => 'fresh',
            ],
        ];

        foreach ($brands as $brands) {
            Brand::create($brands);
        }
    }
}
