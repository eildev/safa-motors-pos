<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Carbon\Carbon;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            [
                'id' => 1,
                'category_id' => 1,
                'size' => 'S',
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'size' => 'M',
            ],
            [
                'id' => 3,
                'category_id' => 1,
                'size' => 'L',
            ],
            [
                'id' => 4,
                'category_id' => 1,
                'size' => 'XL',
            ],
            [
                'id' => 5,
                'category_id' => 1,
                'size' => 'XXL',
            ],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}
