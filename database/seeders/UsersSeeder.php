<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $accountantRole = Role::where('name', 'Accountant')->first();

        User::firstOrCreate([
            'email' => 'admin@accounting.com'
        ], [
            'name' => 'Administrator',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        User::firstOrCreate([
            'email' => 'accountant@accounting.com'
        ], [
            'name' => 'John Accountant',
            'password' => Hash::make('password123'),
            'role_id' => $accountantRole->id,
            'is_active' => true,
        ]);
    }
}