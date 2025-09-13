<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Outlet
        $outlet = Outlet::create([
            'name' => 'Toko Cibubur',
            'code' => 'CBB',
            'address' => 'Jl. Raya Cibubur No. 123, Jakarta Timur',
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);

        // Create Owner User
        $owner = User::create([
            'name' => 'Owner Demo',
            'email' => 'owner@demo.local',
            'password' => Hash::make('password'),
            'outlet_id' => $outlet->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        // Create Cashier User
        $cashier = User::create([
            'name' => 'Cashier Demo',
            'email' => 'cashier@demo.local',
            'password' => Hash::make('password'),
            'outlet_id' => $outlet->id,
            'role' => 'cashier',
            'is_active' => true,
        ]);

        // Create Categories
        $drinkCategory = Category::create([
            'name' => 'Minuman',
            'code' => 'DRK',
        ]);

        $foodCategory = Category::create([
            'name' => 'Makanan',
            'code' => 'FD',
        ]);

        // Create Products
        $products = [
            [
                'outlet_id' => $outlet->id,
                'sku' => 'DRK001',
                'barcode' => '8901234567890',
                'name' => 'Es Teh Manis',
                'category_id' => $drinkCategory->id,
                'unit' => 'gelas',
                'price' => 5000,
                'cost' => 2000,
                'tax_percent' => 10,
                'stock' => 100,
                'min_stock' => 10,
                'is_active' => true,
            ],
            [
                'outlet_id' => $outlet->id,
                'sku' => 'DRK002',
                'barcode' => '8901234567891',
                'name' => 'Es Jeruk',
                'category_id' => $drinkCategory->id,
                'unit' => 'gelas',
                'price' => 7000,
                'cost' => 3000,
                'tax_percent' => 10,
                'stock' => 50,
                'min_stock' => 5,
                'is_active' => true,
            ],
            [
                'outlet_id' => $outlet->id,
                'sku' => 'FD001',
                'barcode' => '8901234567892',
                'name' => 'Nasi Goreng',
                'category_id' => $foodCategory->id,
                'unit' => 'porsi',
                'price' => 15000,
                'cost' => 8000,
                'tax_percent' => 10,
                'stock' => 30,
                'min_stock' => 5,
                'is_active' => true,
            ],
            [
                'outlet_id' => $outlet->id,
                'sku' => 'FD002',
                'barcode' => '8901234567893',
                'name' => 'Mie Ayam',
                'category_id' => $foodCategory->id,
                'unit' => 'porsi',
                'price' => 12000,
                'cost' => 6000,
                'tax_percent' => 10,
                'stock' => 25,
                'min_stock' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Owner login: owner@demo.local / password');
        $this->command->info('Cashier login: cashier@demo.local / password');
        $this->command->info('Outlet: ' . $outlet->name . ' (' . $outlet->code . ')');
    }
}
