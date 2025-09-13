<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    /**
     * Show receipt for printing
     */
    public function show(Sale $sale): View
    {
        // Load all necessary relationships for the receipt
        $sale->load([
            'items.product',
            'payments',
            'outlet',
            'cashier'
        ]);
        
        // Ensure the sale has the basic required data
        if (!$sale->outlet_id || !$sale->cashier_id) {
            abort(422, 'Receipt cannot be generated: missing required sale data');
        }

        return view('receipt', compact('sale'));
    }
}
