<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Dashboard', 'slug' => 'view_dashboard'],
            ['name' => 'View Accounts', 'slug' => 'view_accounts'],
            ['name' => 'Create Accounts', 'slug' => 'create_accounts'],
            ['name' => 'Edit Accounts', 'slug' => 'edit_accounts'],
            ['name' => 'Delete Accounts', 'slug' => 'delete_accounts'],
            ['name' => 'View Journal Entries', 'slug' => 'view_journal_entries'],
            ['name' => 'Create Journal Entries', 'slug' => 'create_journal_entries'],
            ['name' => 'Post Journal Entries', 'slug' => 'post_journal_entries'],
            ['name' => 'View Income', 'slug' => 'view_income'],
            ['name' => 'Create Income', 'slug' => 'create_income'],
            ['name' => 'View Expenses', 'slug' => 'view_expenses'],
            ['name' => 'Create Expenses', 'slug' => 'create_expenses'],
            ['name' => 'View Customers', 'slug' => 'view_customers'],
            ['name' => 'Create Customers', 'slug' => 'create_customers'],
            ['name' => 'View Invoices', 'slug' => 'view_invoices'],
            ['name' => 'Create Invoices', 'slug' => 'create_invoices'],
            ['name' => 'View Payments', 'slug' => 'view_payments'],
            ['name' => 'Create Payments', 'slug' => 'create_payments'],
            ['name' => 'View Reports', 'slug' => 'view_reports'],
            ['name' => 'View Settings', 'slug' => 'view_settings'],
            ['name' => 'Manage Settings', 'slug' => 'manage_settings'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin'], ['description' => 'Full system access']);
        $adminRole->permissions()->sync(Permission::pluck('id'));

        $accountantRole = Role::firstOrCreate(['name' => 'Accountant'], ['description' => 'Manage accounts and transactions']);
        $accountantRole->permissions()->sync(Permission::whereIn('slug', [
            'view_dashboard', 'view_accounts', 'create_accounts', 'edit_accounts',
            'view_journal_entries', 'create_journal_entries', 'post_journal_entries',
            'view_income', 'create_income', 'view_expenses', 'create_expenses',
            'view_customers', 'create_customers', 'view_invoices', 'create_invoices',
            'view_payments', 'create_payments', 'view_reports'
        ])->pluck('id'));

        $staffRole = Role::firstOrCreate(['name' => 'Staff'], ['description' => 'Limited access']);
        $staffRole->permissions()->sync(Permission::whereIn('slug', [
            'view_dashboard', 'view_income', 'view_expenses', 'view_customers', 'view_invoices', 'view_payments'
        ])->pluck('id'));
    }
}