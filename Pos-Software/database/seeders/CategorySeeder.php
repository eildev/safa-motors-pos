<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productCategorys = [
            [
                'id' => 1,
                'name' => 'Clothing',
                'slug' => 'clothing',
            ],
            [
                'id' => 2,
                'name' => 'Electronics',
                'slug' => 'electronics',
            ],
            [
                'id' => 3,
                'name' => 'Food',
                'slug' => 'food',
            ],
        ];

        foreach ($productCategorys as $productCategory) {
            Category::create($productCategory);
        }
    }
}
