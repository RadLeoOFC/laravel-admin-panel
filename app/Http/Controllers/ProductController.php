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

    public function index(Request $request) // Передаем Request в метод
    {
         // Получаем запрос из строки поиска
         $search = $request->input('search');
     
         // Запрашиваем продукты с их категориями и применяем поиск
         $products = Product::with('category')
             ->where('name', 'LIKE', "%$search%")
             ->paginate(10); // Пагинация на 10 элементов
     
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
        // Валидация входных данных
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name', // Название должно быть уникальным
            'price' => 'required|numeric|min:0|max:999999.99', // Ограничение цены
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id', // Проверка, что категория существует
            'new_category' => 'nullable|string|max:255',
        ]);
    
        // Если введена новая категория, проверяем, существует ли она
        if ($request->filled('new_category')) {
            $category = Category::firstOrCreate(['name' => $request->new_category]); // Ищем или создаём
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
        // Валидация входных данных
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id, // Уникальное имя (исключая текущий продукт)
            'price' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id', 
            'new_category' => 'nullable|string|max:255',
        ]);
    
        // Если введена новая категория, ищем её или создаём
        if ($request->filled('new_category')) {
            $category = Category::firstOrCreate(['name' => $request->new_category]); 
            $validated['category_id'] = $category->id;
        }
    
        // Обновление продукта
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

