<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Загрузка всех продуктов вместе с категориями (для оптимизации запросов)
        $products = Product::with('category')->get();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Передаём все категории для выпадающего списка в форме
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id', // Проверка, что категория существует
            'new_category' => 'nullable|string|max:255', // Новая категория (необязательно)
        ]);
    
        // Если введена новая категория, создаём её
        if ($request->filled('new_category')) {
            $category = Category::create(['name' => $request->new_category]);
            $validated['category_id'] = $category->id; // Привязываем продукт к новой категории
        }
    
        // Создаём продукт
        Product::create($validated);
    
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
        // Передаём продукт и категории в форму редактирования
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Добавлена валидация поля category_id
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id', // Проверка, что категория существует
            'new_category' => 'nullable|string|max:255',
        ]);

        if ($request->filled('new_category')) {
            $category = Category::create(['name' => $request->new_category]);
            $validated['category_id'] = $category->id; // Привязываем продукт к новой категории
        }

        // Обновление продукта с привязкой к категории
        $product->update($validated);
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}

