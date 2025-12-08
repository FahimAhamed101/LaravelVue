<?php

namespace App\Http\Controllers\Api;
use App\Models\Size;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(
            Product::with(['colors','sizes','reviews'])->latest()->get())
            ->additional([
                'colors' => Color::has('products')->get(),
                'sizes' => Size::has('products')->get(),
            ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'colors' => 'nullable|array',
            'sizes' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'thumbnail' => 'nullable|image|max:2048',
            'first_image' => 'nullable|image|max:2048',
            'second_image' => 'nullable|image|max:2048',
            'third_image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $productData = $request->only(['name', 'desc', 'qty', 'price', 'status']);
        $productData['slug'] = \Str::slug($request->name);

        // Handle image uploads
        $imageFields = ['thumbnail', 'first_image', 'second_image', 'third_image'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('products', 'public');
                $productData[$field] = 'storage/' . $path;
            }
        }

        $product = Product::create($productData);

        // Attach colors and sizes
        if ($request->has('colors')) {
            $product->colors()->attach($request->colors);
        }
        if ($request->has('sizes')) {
            $product->sizes()->attach($request->sizes);
        }

        return new ProductResource($product->load(['colors', 'sizes', 'reviews']));
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load(['colors', 'sizes', 'reviews']));
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'colors' => 'nullable|array',
            'sizes' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'thumbnail' => 'nullable|image|max:2048',
            'first_image' => 'nullable|image|max:2048',
            'second_image' => 'nullable|image|max:2048',
            'third_image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $productData = $request->only(['name', 'desc', 'qty', 'price', 'status']);
        $productData['slug'] = \Str::slug($request->name);

        // Handle image uploads
        $imageFields = ['thumbnail', 'first_image', 'second_image', 'third_image'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old image if exists
                if ($product->{$field}) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $product->{$field}));
                }
                $path = $request->file($field)->store('products', 'public');
                $productData[$field] = 'storage/' . $path;
            }
        }

        $product->update($productData);

        // Sync colors and sizes
        if ($request->has('colors')) {
            $product->colors()->sync($request->colors);
        }
        if ($request->has('sizes')) {
            $product->sizes()->sync($request->sizes);
        }

        return new ProductResource($product->load(['colors', 'sizes', 'reviews']));
    }

    public function destroy(Product $product)
    {
        // Delete images
        $imageFields = ['thumbnail', 'first_image', 'second_image', 'third_image'];
        foreach ($imageFields as $field) {
            if ($product->{$field}) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->{$field}));
            }
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}