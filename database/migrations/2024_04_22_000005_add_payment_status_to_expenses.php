<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'payment_status')) {
                $table->string('payment_status')->default('unpaid')->after('recurring_end_date');
            }
            if (!Schema::hasColumn('expenses', 'paid_date')) {
                $table->date('paid_date')->nullable()->after('payment_status');
            }
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'paid_date']);
        });
    }
};