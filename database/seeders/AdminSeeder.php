<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Created once. Re-running (e.g. on every deploy) never overwrites a password you changed later.
        User::firstOrCreate(
            ['email' => 'alliybutt3@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => '1234567890',
                'role' => 'admin',
                'account_type' => 'business',
                'organization' => 'Fine Printing Supplies',
                'phone' => '+92 323 0112504',
            ]
        );
    }
}
