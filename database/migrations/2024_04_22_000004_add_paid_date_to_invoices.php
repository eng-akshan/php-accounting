<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('invoices', 'paid_date')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->date('paid_date')->nullable()->after('status');
            });
        }
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('paid_date');
        });
    }
};