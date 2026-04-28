<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchesSeeder extends Seeder
{
    public function run(): void
    {
        Branch::firstOrCreate(['code' => 'HQ'], [
            'name' => 'Headquarters',
            'address' => 'Main Street, City Center',
            'phone' => '+252612345678',
            'email' => 'hq@company.com',
            'is_active' => true,
        ]);

        Branch::firstOrCreate(['code' => 'BR1'], [
            'name' => 'Branch 1',
            'address' => 'District A',
            'phone' => '+252612345679',
            'email' => 'br1@company.com',
            'is_active' => true,
        ]);
    }
}