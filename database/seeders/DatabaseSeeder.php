<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demo = User::create([
            'name'     => 'Demo User',
            'email'    => 'demo@bank.test',
            'password' => Hash::make('password'),
        ]);

        Account::create([
            'user_id' => $demo->id, 'account_number' => 'ACC-1001',
            'balance' => 5000.00, 'minimum_balance' => 100.00,
        ]);
        Account::create([
            'user_id' => $demo->id, 'account_number' => 'ACC-1002',
            'balance' => 250.00, 'minimum_balance' => 100.00,
        ]);

        $other = User::create([
            'name'     => 'Second User',
            'email'    => 'second@bank.test',
            'password' => Hash::make('password'),
        ]);

        Account::create([
            'user_id' => $other->id, 'account_number' => 'ACC-2001',
            'balance' => 1200.00, 'minimum_balance' => 100.00,
        ]);
    }
}
