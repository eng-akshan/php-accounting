<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Acme Corporation', 'email' => 'billing@acme.com', 'phone' => '+252610000001', 'company' => 'Acme Corp'],
            ['name' => 'Global Tech Solutions', 'email' => 'finance@globaltech.com', 'phone' => '+252610000002', 'company' => 'Global Tech'],
            ['name' => 'Safari Internet', 'email' => 'accounts@safari.net', 'phone' => '+252610000003', 'company' => 'Safari ISP'],
            ['name' => 'Mogadishu Trading Co', 'email' => 'info@mtc.so', 'phone' => '+252610000004', 'company' => 'MTC'],
            ['name' => 'Horn of Africa Retail', 'email' => 'payments@horetail.com', 'phone' => '+252610000005', 'company' => 'HoRetail'],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'phone' => $c['phone'],
                    'company' => $c['company'],
                    'is_active' => true,
                ]
            );
        }
    }
}