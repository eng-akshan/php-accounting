<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = ['journal_entry_lines', 'journal_entries', 'expenses', 'incomes', 'invoices', 'payments', 'quotations', 'quotation_items'];
        
        Schema::disableForeignKeyConstraints();
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
        
        Schema::enableForeignKeyConstraints();

        $cashAccount = DB::table('accounts')->where('account_number', '1000')->first();
        $capitalAccount = DB::table('accounts')->where('account_number', '3000')->first();

        if ($cashAccount && $capitalAccount) {
            $entryId = DB::table('journal_entries')->insertGetId([
                'entry_number' => 'JE-OPENING-' . date('Ymd'),
                'date' => date('Y-m-d'),
                'description' => 'Opening Balance - Owner Capital',
                'status' => 'posted',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('journal_entry_lines')->insert([
                [
                    'journal_entry_id' => $entryId,
                    'account_id' => $cashAccount->id,
                    'debit' => 10000.00,
                    'credit' => 0.00,
                    'description' => 'Opening Cash Balance',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'journal_entry_id' => $entryId,
                    'account_id' => $capitalAccount->id,
                    'debit' => 0.00,
                    'credit' => 10000.00,
                    'description' => 'Owner Capital',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down()
    {
        DB::table('journal_entry_lines')->truncate();
        DB::table('journal_entries')->truncate();
    }
};