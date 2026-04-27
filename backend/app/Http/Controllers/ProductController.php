<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Ez kell a fájlkezeléshez!

class ProductController extends Controller
{
    // 1. Összes termék lekérése
    public function index()
    {
        return response()->json(Product::all());
    }

    // 2. Új termék mentése KÉPFELTÖLTÉSSEL
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Maximum 2MB-os kép
        ]);

        $data = $request->except('image');

        // Ha kaptunk fájlt a Vue-tól...
        if ($request->hasFile('image')) {
            // Lementjük a 'products' mappába, és megkapjuk a nevét
            $path = $request->file('image')->store('products', 'public');
            // Ezt az utat mentjük be az adatbázisba (pl. /storage/products/xyz.jpg)
            $data['image_url'] = '/storage/' . $path;
        }

        $product = Product::create($data);

        return response()->json(['message' => 'Termék sikeresen hozzáadva!', 'product' => $product], 201);
    }

    // 3. Termék módosítása KÉPCSERÉVEL
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');

        // Ha a módosításnál új képet töltöttek fel...
        if ($request->hasFile('image')) {
            // Töröljük a régi képet a szerverről (ha volt)
            if ($product->image_url && str_starts_with($product->image_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $product->image_url);
                Storage::disk('public')->delete($oldPath);
            }

            // Lementjük az újat
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $product->update($data);

        return response()->json(['message' => 'Termék sikeresen frissítve!', 'product' => $product]);
    }

    // 4. Termék törlése
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Törlés előtt letöröljük a képet is a szerverről
        if ($product->image_url && str_starts_with($product->image_url, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $product->image_url);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return response()->json(['message' => 'Termék törölve!']);
    }
}
