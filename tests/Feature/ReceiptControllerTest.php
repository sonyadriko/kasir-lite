<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\User;
use App\Services\SaleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReceiptControllerTest extends TestCase
{
    use RefreshDatabase;

    private Sale $sale;
    private Outlet $outlet;
    private User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test outlet
        $this->outlet = Outlet::create([
            'name' => 'Test Store',
            'code' => 'TST',
            'address' => '123 Test Street, Test City',
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);

        // Create test cashier
        $this->cashier = User::create([
            'name' => 'Test Cashier',
            'email' => 'cashier@test.com',
            'password' => Hash::make('password'),
            'outlet_id' => $this->outlet->id,
            'role' => 'cashier',
            'is_active' => true,
        ]);

        // Create test category and products
        $category = Category::create([
            'name' => 'Test Category',
            'code' => 'TEST',
        ]);

        $product1 = Product::create([
            'outlet_id' => $this->outlet->id,
            'sku' => 'TST001',
            'barcode' => '1234567890',
            'name' => 'Test Product 1',
            'category_id' => $category->id,
            'unit' => 'pcs',
            'price' => 15000,
            'cost' => 8000,
            'tax_percent' => 10,
            'stock' => 100,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        $product2 = Product::create([
            'outlet_id' => $this->outlet->id,
            'sku' => 'TST002',
            'barcode' => '1234567891',
            'name' => 'Test Product 2',
            'category_id' => $category->id,
            'unit' => 'pcs',
            'price' => 25000,
            'cost' => 12000,
            'tax_percent' => 10,
            'stock' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        // Create test sale using SaleService
        $saleService = app(SaleService::class);
        $soldAt = Carbon::now();

        $payload = [
            'outlet_id' => $this->outlet->id,
            'sold_at' => $soldAt->toISOString(),
            'items' => [
                [
                    'product_id' => $product1->id,
                    'price' => 15000,
                    'qty' => 2,
                    'discount' => 1000,
                ],
                [
                    'product_id' => $product2->id,
                    'price' => 25000,
                    'qty' => 1,
                    'discount' => 0,
                ],
            ],
            'discount_total' => 2000,
            'tax_percent' => 10,
            'rounding' => 100,
            'payments' => [
                [
                    'method' => 'CASH',
                    'amount' => 50000,
                ],
                [
                    'method' => 'QRIS',
                    'amount' => 5000,
                ],
            ],
            'note' => 'Test receipt sale',
        ];

        $this->sale = $saleService->create($payload, $this->cashier);
    }

    public function test_receipt_displays_successfully_with_all_data(): void
    {
        $response = $this->get(route('receipt.show', $this->sale));

        $response->assertStatus(200);
        $response->assertViewIs('receipt');
        $response->assertViewHas('sale');

        // Verify the sale data is properly loaded
        $saleFromView = $response->viewData('sale');
        $this->assertEquals($this->sale->id, $saleFromView->id);
        
        // Verify relationships are loaded
        $this->assertTrue($saleFromView->relationLoaded('items'));
        $this->assertTrue($saleFromView->relationLoaded('payments'));
        $this->assertTrue($saleFromView->relationLoaded('outlet'));
        $this->assertTrue($saleFromView->relationLoaded('cashier'));
        
        // Verify nested relationships are loaded
        $this->assertTrue($saleFromView->items->first()->relationLoaded('product'));
    }

    public function test_receipt_displays_outlet_information(): void
    {
        $response = $this->get(route('receipt.show', $this->sale));

        $response->assertStatus(200);
        $response->assertSee($this->outlet->name);
        $response->assertSee($this->outlet->address);
    }

    public function test_receipt_displays_invoice_information(): void
    {
        $response = $this->get(route('receipt.show', $this->sale));

        $response->assertStatus(200);
        $response->assertSee($this->sale->invoice_no);
        $response->assertSee($this->sale->sold_at->format('d/m/Y H:i'));
        $response->assertSee($this->cashier->name);
    }

    public function test_receipt_displays_items_correctly(): void
    {
        $response = $this->get(route('receipt.show', $this->sale));

        $response->assertStatus(200);

        // Check that all items are displayed
        foreach ($this->sale->items as $item) {
            $response->assertSee($item->name_snapshot);
            $response->assertSee(number_format($item->qty, 0));
            $response->assertSee(number_format($item->price, 0));
            $response->assertSee(number_format($item->total, 0));
            
            // Check discount if applicable
            if ($item->discount_amount > 0) {
                $response->assertSee(number_format($item->discount_amount, 0));
            }
        }
    }

    public function test_receipt_displays_totals_correctly(): void
    {
        $response = $this->get(route('receipt.show', $this->sale));

        $response->assertStatus(200);
        
        // Check subtotal
        $response->assertSee(number_format($this->sale->subtotal, 0));
        
        // Check discount if applicable
        if ($this->sale->discount_amount > 0) {
            $response->assertSee(number_format($this->sale->discount_amount, 0));
        }
        
        // Check tax if applicable
        if ($this->sale->tax_amount > 0) {
            $response->assertSee(number_format($this->sale->tax_amount, 0));
        }
        
        // Check rounding if applicable
        if ($this->sale->rounding != 0) {
            $response->assertSee(number_format($this->sale->rounding, 0));
        }
        
        // Check final total
        $response->assertSee(number_format($this->sale->total, 0));
    }

    public function test_receipt_displays_payments_correctly(): void
    {
        $response = $this->get(route('receipt.show', $this->sale));

        $response->assertStatus(200);
        
        // Check that all payments are displayed
        foreach ($this->sale->payments as $payment) {
            $response->assertSee($payment->method);
            $response->assertSee(number_format($payment->amount, 0));
        }
        
        // Check change amount if applicable
        if ($this->sale->change_amount > 0) {
            $response->assertSee(number_format($this->sale->change_amount, 0));
        }
    }

    public function test_receipt_displays_note_if_present(): void
    {
        $response = $this->get(route('receipt.show', $this->sale));

        $response->assertStatus(200);
        
        if ($this->sale->note) {
            $response->assertSee($this->sale->note);
        }
    }

    public function test_receipt_has_print_functionality(): void
    {
        $response = $this->get(route('receipt.show', $this->sale));

        $response->assertStatus(200);
        
        // Check that print button exists
        $response->assertSee('Print Receipt');
        $response->assertSee('window.print()');
        
        // Check that print styles are included
        $response->assertSee('@media print');
    }

    public function test_receipt_route_handles_nonexistent_sale(): void
    {
        $response = $this->get(route('receipt.show', 99999));

        $response->assertStatus(404);
    }

    public function test_receipt_handles_sale_with_missing_relationships_gracefully(): void
    {
        // Test that the receipt view can handle cases where relationships might be missing
        // by checking if the controller properly loads relationships
        
        $response = $this->get(route('receipt.show', $this->sale));
        $response->assertStatus(200);
        
        $saleFromView = $response->viewData('sale');
        
        // Ensure all critical relationships are loaded to prevent errors
        $this->assertTrue($saleFromView->relationLoaded('outlet'));
        $this->assertTrue($saleFromView->relationLoaded('cashier'));
        $this->assertTrue($saleFromView->relationLoaded('items'));
        $this->assertTrue($saleFromView->relationLoaded('payments'));
    }
}
