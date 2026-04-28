<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@accounting.com')->first();

        $accounts = [
            ['1000', 'Cash', 'Asset', 'Debit'],
            ['1001', 'Bank', 'Asset', 'Debit'],
            ['1100', 'Accounts Receivable', 'Asset', 'Debit'],
            ['1200', 'Inventory', 'Asset', 'Debit'],
            ['1500', 'Equipment', 'Asset', 'Debit'],
            ['1510', 'Accumulated Depreciation', 'Asset', 'Credit'],

            ['2000', 'Accounts Payable', 'Liability', 'Credit'],
            ['2100', 'Salaries Payable', 'Liability', 'Credit'],
            ['2200', 'Tax Payable', 'Liability', 'Credit'],
            ['2500', 'Notes Payable', 'Liability', 'Credit'],

            ['3000', 'Owners Capital', 'Equity', 'Credit'],
            ['3100', 'Retained Earnings', 'Equity', 'Credit'],
            ['3200', 'Common Stock', 'Equity', 'Credit'],

            ['4000', 'Sales Revenue', 'Revenue', 'Credit'],
            ['4100', 'Service Revenue', 'Revenue', 'Credit'],
            ['4200', 'Interest Income', 'Revenue', 'Credit'],

            ['5000', 'Cost of Goods Sold', 'Expense', 'Debit'],
            ['5100', 'Salaries Expense', 'Expense', 'Debit'],
            ['5200', 'Rent Expense', 'Expense', 'Debit'],
            ['5300', 'Utilities Expense', 'Expense', 'Debit'],
            ['5400', 'Office Supplies Expense', 'Expense', 'Debit'],
            ['5500', 'Depreciation Expense', 'Expense', 'Debit'],
            ['5600', 'Marketing Expense', 'Expense', 'Debit'],
            ['5700', 'Transportation Expense', 'Expense', 'Debit'],
            ['5800', 'Miscellaneous Expense', 'Expense', 'Debit'],
        ];

        foreach ($accounts as $acc) {
            Account::updateOrCreate(
                ['account_number' => $acc[0]],
                [
                    'name' => $acc[1],
                    'type' => $acc[2],
                    'nature' => $acc[3],
                    'created_by' => $admin->id,
                    'is_active' => true,
                    'allow_transaction' => true,
                ]
            );
        }
    }
}