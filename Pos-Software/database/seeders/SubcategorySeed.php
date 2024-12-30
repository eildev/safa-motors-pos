<?php

namespace Database\Seeders;

use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubcategorySeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productSubcategory = [
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'T-shirt',
                'slug' => 't-shirt',
            ],
            [
                'id' => 2,
                'category_id' => 2,
                'name' => 'TV',
                'slug' => 'tv',
            ],
            [
                'id' => 3,
                'category_id' => 3,
                'name' => 'Vegetable',
                'slug' => 'vegetable',
            ],
        ];

        foreach ($productSubcategory as $subcategory) {
            SubCategory::create($subcategory);
        }
    }
}
