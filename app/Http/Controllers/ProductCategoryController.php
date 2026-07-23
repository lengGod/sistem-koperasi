<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_stock');
        
        $query = ProductCategory::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->latest()->get();
        return view('product-categories.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('manage_products');
        return view('product-categories.create');
    }

    public function store(StoreProductCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        
        ProductCategory::create($data);
        return redirect()->route('product-categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(ProductCategory $productCategory)
    {
        Gate::authorize('view_stock');
        return view('product-categories.show', compact('productCategory'));
    }

    public function edit(ProductCategory $productCategory)
    {
        Gate::authorize('manage_products');
        return view('product-categories.edit', compact('productCategory'));
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        $data = $request->validated();
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        
        $productCategory->update($data);
        return redirect()->route('product-categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        Gate::authorize('manage_products');
        $productCategory->delete();
        return redirect()->route('product-categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('manage_products');

        $validated = $request->validate([
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'distinct', 'exists:product_categories,id'],
        ]);

        ProductCategory::whereIn('id', $validated['category_ids'])->delete();

        return redirect()->route('product-categories.index')->with('success', 'Kategori terpilih berhasil dihapus.');
    }
}
