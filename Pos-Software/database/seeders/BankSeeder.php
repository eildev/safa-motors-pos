<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'id' => 1,
                'branch_id' => 1,
                'name' => 'Cash',
                'bank_branch_name' => 'Dhaka',
                'bank_branch_manager_name' => 'No Name',
                'bank_branch_phone' => '01743877834',
                'bank_account_number' => '547896',
                'bank_branch_email' => 'demo@gmail.com',
                'opening_balance' => '00',
            ],
            [
                'id' => 2,
                'branch_id' => 1,
                'name' => 'BKash',
                'bank_branch_name' => 'Dhaka',
                'bank_branch_manager_name' => 'No Name',
                'bank_branch_phone' => '01743877834',
                'bank_account_number' => '547896',
                'bank_branch_email' => 'demo@gmail.com',
                'opening_balance' => '00',
            ],
            [
                'id' => 3,
                'branch_id' => 1,
                'name' => 'Nagad',
                'bank_branch_name' => 'Dhaka',
                'bank_branch_manager_name' => 'No Name',
                'bank_branch_phone' => '01743877834',
                'bank_account_number' => '547896',
                'bank_branch_email' => 'demo@gmail.com',
                'opening_balance' => '00',
            ],
            [
                'id' => 4,
                'branch_id' => 1,
                'name' => 'Rocket',
                'bank_branch_name' => 'Dhaka',
                'bank_branch_manager_name' => 'No Name',
                'bank_branch_phone' => '01743877834',
                'bank_account_number' => '547896',
                'bank_branch_email' => 'demo@gmail.com',
                'opening_balance' => '00',
            ],
        ];
        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
