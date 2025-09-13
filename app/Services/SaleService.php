<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {
    }

    /**
     * Create a new sale with items and payments
     */
    public function create(array $payload, User $cashier): Sale
    {
        return DB::transaction(function () use ($payload, $cashier) {
            $outlet = $cashier->outlet;
            $soldAt = Carbon::parse($payload['sold_at']);

            // Generate invoice number
            $invoiceNo = $this->invoiceService->nextNumber($outlet, $soldAt);

            // Calculate totals
            $calculations = $this->calculateTotals($payload);

            // Create sale
            $sale = Sale::create([
                'outlet_id' => $payload['outlet_id'],
                'cashier_id' => $cashier->id,
                'invoice_no' => $invoiceNo,
                'sold_at' => $soldAt,
                'subtotal' => $calculations['subtotal'],
                'discount_amount' => $calculations['discount_total'],
                'tax_amount' => $calculations['tax_amount'],
                'rounding' => $calculations['rounding'],
                'total' => $calculations['total'],
                'paid' => $calculations['paid'],
                'change_amount' => $calculations['change'],
                'status' => 'PAID',
                'channel' => 'POS',
                'note' => $payload['note'] ?? null,
            ]);

            // Create sale items and update stock
            $this->createSaleItems($sale, $payload['items'], $soldAt, $cashier);

            // Create payments
            $this->createPayments($sale, $payload['payments'], $soldAt);

            return $sale->load(['items', 'payments']);
        });
    }

    private function calculateTotals(array $payload): array
    {
        $subtotal = 0;
        $taxAmount = 0;

        // Calculate item totals
        foreach ($payload['items'] as $item) {
            $itemSubtotal = $item['price'] * $item['qty'];
            $itemDiscount = $item['discount'] ?? 0;
            $itemAfterDiscount = $itemSubtotal - $itemDiscount;
            
            $subtotal += $itemAfterDiscount;
        }

        // Apply global discount
        $discountTotal = $payload['discount_total'] ?? 0;
        $afterGlobalDiscount = $subtotal - $discountTotal;

        // Calculate tax
        $taxPercent = $payload['tax_percent'] ?? 0;
        $taxAmount = round($afterGlobalDiscount * ($taxPercent / 100), 2);

        // Apply rounding
        $rounding = $payload['rounding'] ?? 0;
        $total = $afterGlobalDiscount + $taxAmount + $rounding;

        // Calculate payments
        $paid = 0;
        foreach ($payload['payments'] as $payment) {
            $paid += $payment['amount'];
        }

        $change = $paid - $total;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'tax_amount' => $taxAmount,
            'rounding' => round($rounding, 2),
            'total' => round($total, 2),
            'paid' => round($paid, 2),
            'change' => round($change, 2),
        ];
    }

    private function createSaleItems(Sale $sale, array $items, Carbon $soldAt, User $cashier): void
    {
        foreach ($items as $itemData) {
            // Lock product for update
            $product = Product::where('id', $itemData['product_id'])
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw new \Exception("Product not found: {$itemData['product_id']}");
            }

            // Calculate item totals
            $itemSubtotal = $itemData['price'] * $itemData['qty'];
            $itemDiscount = $itemData['discount'] ?? 0;
            $itemTotal = $itemSubtotal - $itemDiscount;

            // Calculate item tax (proportional to item total)
            $taxPercent = $sale->tax_amount > 0 ? 
                ($sale->tax_amount / ($sale->subtotal - $sale->discount_amount)) * 100 : 0;
            $itemTaxAmount = round($itemTotal * ($taxPercent / 100), 2);

            // Create sale item
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'name_snapshot' => $product->name,
                'price' => $itemData['price'],
                'qty' => $itemData['qty'],
                'discount_amount' => $itemDiscount,
                'tax_amount' => $itemTaxAmount,
                'total' => round($itemTotal + $itemTaxAmount, 2),
            ]);

            // Update stock
            $newStock = $product->stock - $itemData['qty'];
            $product->update(['stock' => $newStock]);

            // Record stock movement
            StockMovement::create([
                'product_id' => $product->id,
                'outlet_id' => $sale->outlet_id,
                'type' => 'SALE',
                'qty' => -$itemData['qty'],
                'ref_type' => 'sale',
                'ref_id' => $sale->id,
                'note' => "Sale: {$sale->invoice_no}",
                'moved_at' => $soldAt,
                'user_id' => $cashier->id,
            ]);
        }
    }

    private function createPayments(Sale $sale, array $payments, Carbon $soldAt): void
    {
        foreach ($payments as $paymentData) {
            Payment::create([
                'sale_id' => $sale->id,
                'method' => $paymentData['method'],
                'amount' => $paymentData['amount'],
                'reference_no' => $paymentData['reference_no'] ?? null,
                'paid_at' => $soldAt,
            ]);
        }
    }
}