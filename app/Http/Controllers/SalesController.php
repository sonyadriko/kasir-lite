<?php

namespace App\Http\Controllers;

use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    public function __construct(
        private SaleService $saleService
    ) {
    }

    /**
     * Create a new sale
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id',
                'sold_at' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.discount' => 'numeric|min:0',
                'discount_total' => 'numeric|min:0',
                'tax_percent' => 'numeric|min:0|max:100',
                'rounding' => 'numeric',
                'payments' => 'required|array|min:1',
                'payments.*.method' => 'required|in:CASH,QRIS,EDC,TRANSFER,EWALLET',
                'payments.*.amount' => 'required|numeric|min:0.01',
                'payments.*.reference_no' => 'nullable|string',
                'note' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get user for the transaction - fallback to demo user for development
            $user = $request->user();
            if (!$user) {
                // Development mode - use demo cashier
                $user = \App\Models\User::where('role', 'cashier')->first();
                if (!$user) {
                    return response()->json([
                        'error' => 'No cashier available. Please run: php artisan migrate:fresh --seed'
                    ], 500);
                }
            }
            
            $sale = $this->saleService->create($request->all(), $user);

            return response()->json([
                'sale_id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'total' => $sale->total,
                'paid' => $sale->paid,
                'change' => $sale->change_amount,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create sale',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
