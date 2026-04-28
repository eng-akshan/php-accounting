<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@accounting.com')->first();
        $branch = Branch::first();

        CompanySetting::firstOrCreate([], [
            'company_name' => 'Redsom Group',
            'address' => 'Mogadishu, Somalia\nBakara Market',
            'phone' => '+252 61 1234567',
            'email' => 'info@redsomgroup.com',
            'currency' => 'USD',
            'tax_rate' => 10,
            'invoice_prefix' => 'INV',
            'quotation_prefix' => 'QUO',
        ]);

        $cash = Account::where('account_number', '1000')->first();
        $bank = Account::where('account_number', '1001')->first();
        $revenue = Account::where('account_number', '4000')->first();
        $expense = Account::where('account_number', '5000')->first();
        $ar = Account::where('account_number', '1100')->first();

        Income::create([
            'date' => Carbon::now()->subDays(5),
            'description' => 'Internet Subscription - Monthly Service',
            'amount' => 150.00,
            'account_id' => $revenue->id,
            'created_by' => $admin->id,
            'branch_id' => $branch?->id,
        ]);

        JournalEntry::create([
            'entry_number' => 'JE-20260420-001',
            'date' => Carbon::now()->subDays(5),
            'description' => 'Record internet subscription income',
            'status' => 'posted',
            'created_by' => $admin->id,
        ])->lines()->createMany([
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 150.00, 'description' => 'Service income'],
            ['account_id' => $cash->id, 'debit' => 150.00, 'credit' => 0, 'description' => 'Cash received'],
        ]);

        Expense::create([
            'date' => Carbon::now()->subDays(3),
            'description' => 'Electric Bill - Monthly',
            'amount' => 85.50,
            'account_id' => $expense->id,
            'created_by' => $admin->id,
            'branch_id' => $branch?->id,
        ]);

        JournalEntry::create([
            'entry_number' => 'JE-20260420-002',
            'date' => Carbon::now()->subDays(3),
            'description' => 'Pay electric bill',
            'status' => 'posted',
            'created_by' => $admin->id,
        ])->lines()->createMany([
            ['account_id' => $expense->id, 'debit' => 85.50, 'credit' => 0, 'description' => 'Electric expense'],
            ['account_id' => $cash->id, 'debit' => 0, 'credit' => 85.50, 'description' => 'Cash paid'],
        ]);

        $customer = Customer::first();

        if ($customer) {
            $invoice = Invoice::create([
                'invoice_number' => 'INV-20260420-001',
                'customer_id' => $customer->id,
                'issue_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->addDays(20),
                'subtotal' => 500.00,
                'tax_amount' => 50.00,
                'discount_amount' => 0,
                'total' => 550.00,
                'paid_amount' => 0,
                'status' => 'sent',
                'notes' => 'Thank you for your business!',
                'created_by' => $admin->id,
                'branch_id' => $branch?->id,
            ]);

            $invoice->items()->createMany([
                ['description' => 'Web Development Service', 'quantity' => 1, 'unit_price' => 300.00, 'total' => 300.00],
                ['description' => 'Hosting (1 Year)', 'quantity' => 1, 'unit_price' => 200.00, 'total' => 200.00],
            ]);

            $quotation = Quotation::create([
                'quotation_number' => 'QUO-20260420-001',
                'customer_id' => $customer->id,
                'issue_date' => Carbon::now()->subDays(2),
                'valid_until' => Carbon::now()->addDays(28),
                'subtotal' => 1200.00,
                'tax_amount' => 120.00,
                'discount_amount' => 0,
                'total' => 1320.00,
                'status' => 'sent',
                'user_id' => $admin->id,
                'branch_id' => $branch?->id,
                'notes' => 'Includes 1 year free support',
                'terms' => 'Payment due within 30 days. Quote valid for 30 days.',
            ]);

            $quotation->items()->createMany([
                ['description' => 'E-Commerce Website Development', 'quantity' => 1, 'unit_price' => 1000.00, 'total' => 1000.00],
                ['description' => 'SSL Certificate', 'quantity' => 1, 'unit_price' => 100.00, 'total' => 100.00],
                ['description' => 'Maintenance (6 Months)', 'quantity' => 1, 'unit_price' => 100.00, 'total' => 100.00],
            ]);
        }
    }
}