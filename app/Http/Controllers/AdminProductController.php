<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('outlet_id', auth()->user()->outlet_id)
            ->orderBy('name')
            ->paginate(20);
            
        $categories = Category::where('outlet_id', auth()->user()->outlet_id)->get();
        
        return view('admin.products.index', compact('products', 'categories'));
    }
    
    public function create()
    {
        $categories = Category::where('outlet_id', auth()->user()->outlet_id)->get();
        return view('admin.products.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $validated['outlet_id'] = auth()->user()->outlet_id;
        $validated['is_active'] = $request->has('is_active');
        
        Product::create($validated);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }
    
    public function show(Product $product)
    {
        $this->authorizeProduct($product);
        
        $stockMovements = $product->stockMovements()
            ->with('sale')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
            
        return view('admin.products.show', compact('product', 'stockMovements'));
    }
    
    public function edit(Product $product)
    {
        $this->authorizeProduct($product);
        
        $categories = Category::where('outlet_id', auth()->user()->outlet_id)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }
    
    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        $product->update($validated);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }
    
    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);
        
        // Check if product has sales
        if ($product->saleItems()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete product that has sales records.');
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
    
    public function adjustStock(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        
        $validated = $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255'
        ]);
        
        DB::transaction(function () use ($product, $validated) {
            $oldStock = $product->stock;
            $newStock = $oldStock + $validated['adjustment'];
            
            if ($newStock < 0) {
                throw new \Exception('Stock cannot be negative');
            }
            
            $product->update(['stock' => $newStock]);
            
            // Create stock movement record
            $product->stockMovements()->create([
                'type' => $validated['adjustment'] > 0 ? 'IN' : 'OUT',
                'quantity' => abs($validated['adjustment']),
                'reason' => $validated['reason'],
                'reference_type' => 'manual_adjustment',
                'user_id' => auth()->id()
            ]);
        });
        
        return redirect()->back()
            ->with('success', 'Stock adjusted successfully.');
    }
    
    private function authorizeProduct(Product $product)
    {
        if ($product->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Unauthorized access to product.');
        }
    }
}