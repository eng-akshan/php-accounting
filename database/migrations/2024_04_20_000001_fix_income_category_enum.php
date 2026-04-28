<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE incomes MODIFY COLUMN category ENUM('sales', 'service', 'rent', 'interest', 'other') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE incomes MODIFY COLUMN category ENUM('sales', 'services', 'rent', 'interest', 'other') NOT NULL");
    }
};