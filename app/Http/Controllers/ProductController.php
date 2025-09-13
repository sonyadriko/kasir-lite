<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Get products with search and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['category', 'outlet']);

        // Search by name, SKU, or barcode
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Search by barcode specifically
        if ($barcode = $request->get('barcode')) {
            $query->where('barcode', $barcode);
        }

        $products = $query->paginate(15);

        return response()->json($products);
    }
}
