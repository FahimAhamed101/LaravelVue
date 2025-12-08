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
            Product::with(['colors','sizes'])->latest()->get())
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
            'status' => 'required',
            // Changed from 'image' to 'string' to accept URLs
            'thumbnail' => 'nullable|string|max:500',
            'first_image' => 'nullable|string|max:500',
            'second_image' => 'nullable|string|max:500',
            'third_image' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $productData = $request->only(['name', 'desc', 'qty', 'price', 'status']);
        $productData['slug'] = \Str::slug($request->name);

        // Handle image URLs (no file upload processing needed)
        $imageFields = ['thumbnail', 'first_image', 'second_image', 'third_image'];
        foreach ($imageFields as $field) {
            if ($request->filled($field)) {
                $productData[$field] = $request->input($field);
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

        return response()->json([
            'message' => 'Product created successfully',
            'data' => new ProductResource($product->load(['colors', 'sizes']))
        ], 201);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load(['colors', 'sizes']));
    }

    public function update(Request $request, $id)
{
    // Validate request
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'desc' => 'nullable|string',
        'qty' => 'required|integer|min:0',
        'price' => 'required|numeric|min:0',
        'status' => 'required|integer|in:0,1',
        'thumbnail' => 'required|string',
        'first_image' => 'nullable|string',
        'second_image' => 'nullable|string',
        'third_image' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
            'message' => 'Validation failed'
        ], 422);
    }

    try {
        // Find the product
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'product_id' => $id
            ], 404);
        }
        
        // Update product
        $product->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'desc' => $request->desc,
            'qty' => $request->qty,
            'price' => $request->price,
            'status' => $request->status,
            'thumbnail' => $request->thumbnail,
            'first_image' => $request->first_image,
            'second_image' => $request->second_image,
            'third_image' => $request->third_image,
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product)
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error updating product',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // In ProductController.php
public function destroy($id)
{
    try {
        // Try to find the product
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'product_id' => $id
            ], 404);
        }
        
        // Delete the product
        $product->delete();
        
        return response()->json([
            'message' => 'Product deleted successfully',
            'deleted_id' => $id
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error deleting product',
            'error' => $e->getMessage(),
            'product_id' => $id
        ], 500);
    }
}
}