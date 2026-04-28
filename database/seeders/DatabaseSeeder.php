<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesPermissionsSeeder::class,
            UsersSeeder::class,
            AccountsSeeder::class,
            BranchesSeeder::class,
            CustomersSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}