<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Psize;
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
            'created_at' => Carbon::now(),
            ],
            [
            'id' => 2,
            'category_id' => 1,
            'size' => 'M',
            'created_at' => Carbon::now(),
            ],
            [
            'id' => 3,
            'category_id' => 1,
            'size' => 'L',
            'created_at' => Carbon::now(),
            ],
            [
            'id' => 4,
            'category_id' => 1,
            'size' => 'XL',
            'created_at' => Carbon::now(),
            ],
            [
            'id' => 5,
            'category_id' => 1,
            'size' => 'XXL',
            'created_at' => Carbon::now(),
            ],
        ];

        foreach ($sizes as $size) {
            Psize::create($size);
        }
    }
}
