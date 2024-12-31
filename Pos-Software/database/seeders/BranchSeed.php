<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'name' => 'Eclipse Blends and Blossom',
            'slug' => 'eclipse-blends-and-blossom',
            'address' => 'House 41, Road 6, Block E, Banasree, Rampura, Dhaka, Bangladesh',
            'email' => 'ebb@gmail.com',
            'phone' => '01917344267',
        ]);
    }
}
