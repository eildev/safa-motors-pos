<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
Use Carbon\Carbon;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'Super Admin', 'guard_name' => 'web', 'created_at' => Carbon::now()],
            ['id' => 2, 'name' => 'Admin', 'guard_name' => 'web', 'created_at' => Carbon::now()],
            ['id' => 3, 'name' => 'Demo', 'guard_name' => 'web', 'created_at' => Carbon::now()],
            // add more Role as needed
        ];

        // Insert permissions into the database
        DB::table('roles')->insert($roles);
    }
}
