<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\InvoiceSequence;
use App\Services\SaleService;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    private SaleService $saleService;
    private Outlet $outlet;
    private User $cashier;
    private Product $product1;
    private Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->saleService = $this->app->make(SaleService::class);

        // Create test data
        $this->outlet = Outlet::create([
            'name' => 'Test Outlet',
            'code' => 'TST',
            'address' => 'Test Address',
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);

        $this->cashier = User::create([
            'name' => 'Test Cashier',
            'email' => 'cashier@test.com',
            'password' => Hash::make('password'),
            'outlet_id' => $this->outlet->id,
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'code' => 'TEST',
        ]);

        $this->product1 = Product::create([
            'outlet_id' => $this->outlet->id,
            'sku' => 'TST001',
            'barcode' => '1234567890',
            'name' => 'Test Product 1',
            'category_id' => $category->id,
            'unit' => 'pcs',
            'price' => 10000,
            'cost' => 5000,
            'tax_percent' => 10,
            'stock' => 100,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        $this->product2 = Product::create([
            'outlet_id' => $this->outlet->id,
            'sku' => 'TST002',
            'barcode' => '1234567891',
            'name' => 'Test Product 2',
            'category_id' => $category->id,
            'unit' => 'pcs',
            'price' => 15000,
            'cost' => 8000,
            'tax_percent' => 10,
            'stock' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_create_sale_with_single_item_and_single_payment(): void
    {
        $soldAt = Carbon::now();
        $payload = [
            'outlet_id' => $this->outlet->id,
            'sold_at' => $soldAt->toISOString(),
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'price' => 10000,
                    'qty' => 2,
                    'discount' => 0,
                ],
            ],
            'discount_total' => 0,
            'tax_percent' => 10,
            'rounding' => 0,
            'payments' => [
                [
                    'method' => 'CASH',
                    'amount' => 22000,
                ],
            ],
            'note' => 'Test sale',
        ];

        $sale = $this->saleService->create($payload, $this->cashier);

        // Assert sale created
        $this->assertInstanceOf(Sale::class, $sale);
        $this->assertEquals($this->outlet->id, $sale->outlet_id);
        $this->assertEquals($this->cashier->id, $sale->cashier_id);
        $this->assertEquals('TST/' . $soldAt->format('Ym') . '/0001', $sale->invoice_no);
        $this->assertEquals(20000, $sale->subtotal); // 2 * 10000
        $this->assertEquals(0, $sale->discount_amount);
        $this->assertEquals(2000, $sale->tax_amount); // 20000 * 10%
        $this->assertEquals(0, $sale->rounding);
        $this->assertEquals(22000, $sale->total); // 20000 + 2000
        $this->assertEquals(22000, $sale->paid);
        $this->assertEquals(0, $sale->change_amount);
        $this->assertEquals('PAID', $sale->status);

        // Assert sale items created
        $this->assertCount(1, $sale->items);
        $saleItem = $sale->items->first();
        $this->assertEquals($this->product1->id, $saleItem->product_id);
        $this->assertEquals('Test Product 1', $saleItem->name_snapshot);
        $this->assertEquals(10000, $saleItem->price);
        $this->assertEquals(2, $saleItem->qty);
        $this->assertEquals(0, $saleItem->discount_amount);
        $this->assertEquals(2000, $saleItem->tax_amount);
        $this->assertEquals(22000, $saleItem->total);

        // Assert payments created
        $this->assertCount(1, $sale->payments);
        $payment = $sale->payments->first();
        $this->assertEquals('CASH', $payment->method);
        $this->assertEquals(22000, $payment->amount);

        // Assert stock decremented
        $this->product1->refresh();
        $this->assertEquals(98, $this->product1->stock); // 100 - 2

        // Assert stock movement created
        $stockMovement = StockMovement::where('product_id', $this->product1->id)
            ->where('type', 'SALE')
            ->first();
        $this->assertNotNull($stockMovement);
        $this->assertEquals(-2, $stockMovement->qty);
        $this->assertEquals('sale', $stockMovement->ref_type);
        $this->assertEquals($sale->id, $stockMovement->ref_id);
    }

    public function test_create_sale_with_multiple_items_and_payments(): void
    {
        $soldAt = Carbon::now();
        $payload = [
            'outlet_id' => $this->outlet->id,
            'sold_at' => $soldAt->toISOString(),
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'price' => 10000,
                    'qty' => 2,
                    'discount' => 1000,
                ],
                [
                    'product_id' => $this->product2->id,
                    'price' => 15000,
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
                    'amount' => 30000,
                ],
                [
                    'method' => 'QRIS',
                    'amount' => 5000,
                ],
            ],
            'note' => 'Multi-item sale',
        ];

        $sale = $this->saleService->create($payload, $this->cashier);

        // Calculate expected totals:
        // Item 1: (10000 * 2) - 1000 = 19000
        // Item 2: (15000 * 1) - 0 = 15000
        // Subtotal: 19000 + 15000 = 34000
        // After global discount: 34000 - 2000 = 32000
        // Tax: 32000 * 10% = 3200
        // Total: 32000 + 3200 + 100 = 35300
        // Paid: 30000 + 5000 = 35000
        // Change: 35000 - 35300 = -300 (short payment)

        $this->assertEquals(34000, $sale->subtotal);
        $this->assertEquals(2000, $sale->discount_amount);
        $this->assertEquals(3200, $sale->tax_amount);
        $this->assertEquals(100, $sale->rounding);
        $this->assertEquals(35300, $sale->total);
        $this->assertEquals(35000, $sale->paid);
        $this->assertEquals(-300, $sale->change_amount);

        // Assert items created
        $this->assertCount(2, $sale->items);

        // Assert payments created
        $this->assertCount(2, $sale->payments);
        $cashPayment = $sale->payments->where('method', 'CASH')->first();
        $qrisPayment = $sale->payments->where('method', 'QRIS')->first();
        $this->assertEquals(30000, $cashPayment->amount);
        $this->assertEquals(5000, $qrisPayment->amount);

        // Assert stock decremented for both products
        $this->product1->refresh();
        $this->product2->refresh();
        $this->assertEquals(98, $this->product1->stock); // 100 - 2
        $this->assertEquals(49, $this->product2->stock); // 50 - 1

        // Assert stock movements created for both products
        $stockMovements = StockMovement::where('type', 'SALE')
            ->where('ref_id', $sale->id)
            ->get();
        $this->assertCount(2, $stockMovements);
    }

    public function test_invoice_number_increments_correctly(): void
    {
        $soldAt = Carbon::now();
        $payload = [
            'outlet_id' => $this->outlet->id,
            'sold_at' => $soldAt->toISOString(),
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'price' => 10000,
                    'qty' => 1,
                    'discount' => 0,
                ],
            ],
            'discount_total' => 0,
            'tax_percent' => 0,
            'rounding' => 0,
            'payments' => [
                [
                    'method' => 'CASH',
                    'amount' => 10000,
                ],
            ],
        ];

        // Create first sale
        $sale1 = $this->saleService->create($payload, $this->cashier);
        $this->assertEquals('TST/' . $soldAt->format('Ym') . '/0001', $sale1->invoice_no);

        // Create second sale
        $sale2 = $this->saleService->create($payload, $this->cashier);
        $this->assertEquals('TST/' . $soldAt->format('Ym') . '/0002', $sale2->invoice_no);

        // Verify invoice sequence record
        $sequence = InvoiceSequence::where('outlet_id', $this->outlet->id)
            ->where('period', $soldAt->format('Ym'))
            ->first();
        $this->assertNotNull($sequence);
        $this->assertEquals(2, $sequence->last_number);
    }

    public function test_different_months_have_separate_invoice_sequences(): void
    {
        $soldAt1 = Carbon::parse('2025-01-15');
        $soldAt2 = Carbon::parse('2025-02-15');
        
        $payload = [
            'outlet_id' => $this->outlet->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'price' => 10000,
                    'qty' => 1,
                    'discount' => 0,
                ],
            ],
            'discount_total' => 0,
            'tax_percent' => 0,
            'rounding' => 0,
            'payments' => [
                [
                    'method' => 'CASH',
                    'amount' => 10000,
                ],
            ],
        ];

        // January sale
        $payload['sold_at'] = $soldAt1->toISOString();
        $sale1 = $this->saleService->create($payload, $this->cashier);
        $this->assertEquals('TST/202501/0001', $sale1->invoice_no);

        // February sale
        $payload['sold_at'] = $soldAt2->toISOString();
        $sale2 = $this->saleService->create($payload, $this->cashier);
        $this->assertEquals('TST/202502/0001', $sale2->invoice_no);
    }
}
