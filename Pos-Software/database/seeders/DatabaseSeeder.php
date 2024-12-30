<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory()->create([
        // 'name' => 'Test User',
        // 'email' => 'test@example.com',
        // ]);
        $this->call([
            BranchSeed::class,
            UserSeed::class,
            SettingSeed::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            RoleHasPermission::class,
            ModelHasRolesSeeder::class,
            BankSeeder::class,
            CategorySeeder::class,
            SizeSeeder::class,
            UnitSeeder::class,
            ExpenseCategorySeeder::class,
            SubcategorySeed::class,
            BrandSeed::class,
        ]);
    }
}
