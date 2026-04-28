<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up()
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Administrator',
            'email' => 'admin@accounting.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roleId = DB::table('roles')->where('name', 'Admin')->value('id');
        
        if ($roleId) {
            DB::table('users')->where('id', $userId)->update(['role_id' => $roleId]);
        }

        echo "Test user created: admin@accounting.com / password123\n";
    }

    public function down()
    {
        DB::table('users')->where('email', 'admin@accounting.com')->delete();
    }
};