<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function show($id)
    {
        return response()->json(Product::findOrFail($id));
    }

    public function store(Request $request)
    {
        // Később itt lehet middleware-ben is ellenőrizni a jogosultságot
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Nincs jogosultságod'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer',
            'image_url' => 'nullable|string'
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Nincs jogosultságod'], 403);
        }

        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json($product);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Nincs jogosultságod'], 403);
        }

        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Termék törölve']);
    }
}
