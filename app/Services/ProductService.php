<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;

class ProductService
{
    public function getAllProducts($search = null)
    {
        $query = Product::with('category');
        if ($search) {
            $query->where('name', 'LIKE', "%$search%");
        }
        return $query->paginate(10);
    }

    public function createProduct($data)
    {
        if (!empty($data['new_category'])) {
            $category = Category::firstOrCreate(['name' => $data['new_category']]);
            $data['category_id'] = $category->id;
        }

        return Product::create($data);
    }

    public function updateProduct(Product $product, $data)
    {
        if (!empty($data['new_category'])) {
            $category = Category::firstOrCreate(['name' => $data['new_category']]);
            $data['category_id'] = $category->id;
        }

        return $product->update($data);
    }

    public function deleteProduct(Product $product)
    {
        return $product->delete();
    }
}
