<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Support\Facades\Gate;

class ProductCategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('view_stock');
        $categories = ProductCategory::all();
        return view('product-categories.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('manage_products');
        return view('product-categories.create');
    }

    public function store(StoreProductCategoryRequest $request)
    {
        ProductCategory::create($request->validated());
        return redirect()->route('product-categories.index')->with('success', 'Category created successfully.');
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
        $productCategory->update($request->validated());
        return redirect()->route('product-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        Gate::authorize('manage_products');
        $productCategory->delete();
        return redirect()->route('product-categories.index')->with('success', 'Category deleted successfully.');
    }
}
