<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1); // Get the current page (default is 1)
    
        // Include the page in the cache key to store separate cache entries for each page
        $cacheKey = "products_search_{$search}_page_{$page}";
    
        $products = Cache::remember($cacheKey, 600, function () use ($search) {
            return $this->productService->getAllProducts($search);
        });
    
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Passing all categories to the drop-down list in the form
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'new_category' => 'nullable|string|max:255',
        ]);

        $this->productService->createProduct($validated);

        // Clear the cache after adding a new product
        Cache::forget("products_search_");

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // We transfer the product and categories to the editing form
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'price' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'new_category' => 'nullable|string|max:255',
        ]);

        $this->productService->updateProduct($product, $validated);

        // Clear cache after updating a product
        Cache::forget("products_search_");

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);

        // Clear cache after deleting a product
        Cache::forget("products_search_");

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
