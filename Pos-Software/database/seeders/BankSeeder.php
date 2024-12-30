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
                'branch_name' => 'Dhaka',
                'manager_name' => 'No Name',
                'phone_number' => '01743877834',
                'account' => '547896',
                'email' => 'demo@gmail.com',
                'opening_balance' => '00',
                'purpose' => 'Cash',
            ],
            [
                'id' => 2,
                'branch_id' => 1,
                'name' => 'BKash',
                'branch_name' => 'Dhaka',
                'manager_name' => 'No Name',
                'phone_number' => '01956435464',
                'account' => '357159',
                'email' => 'demo@gmail.com',
                'opening_balance' => '00',
                'purpose' => 'bKash',
            ],
            [
                'id' => 3,
                'branch_id' => 1,
                'name' => 'Nagad',
                'branch_name' => 'Dhaka',
                'manager_name' => 'No Name',
                'phone_number' => '01956489144',
                'account' => '654456',
                'email' => 'demo@gmail.com',
                'opening_balance' => '00',
                'purpose' => 'Nagad',
            ],
            [
                'id' => 4,
                'branch_id' => 1,
                'name' => 'Rocket',
                'branch_name' => 'Dhaka',
                'manager_name' => 'No Name',
                'phone_number' => '01856932478',
                'account' => '258456',
                'email' => 'demo@gmail.com',
                'opening_balance' => '00',
                'purpose' => 'rocket',
            ],
        ];
        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
