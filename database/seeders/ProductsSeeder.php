<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['SKU001', 'Widget A', 'General', 'pcs', 25.00, 10.00, 100, 10],
            ['SKU002', 'Widget B', 'General', 'pcs', 35.00, 15.00, 75, 10],
            ['SKU003', 'Gadget X', 'Electronics', 'pcs', 150.00, 75.00, 50, 5],
            ['SKU004', 'Tool Set', 'Tools', 'box', 89.00, 45.00, 30, 5],
            ['SKU005', 'Component Y', 'Electronics', 'pcs', 12.00, 5.00, 200, 20],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['sku' => $p[0]],
                [
                    'name' => $p[1],
                    'category' => $p[2],
                    'unit' => $p[3],
                    'price' => $p[4],
                    'cost' => $p[5],
                    'quantity' => $p[6],
                    'min_stock' => $p[7],
                    'is_active' => true,
                    'created_by' => 1,
                ]
            );
        }
    }
}