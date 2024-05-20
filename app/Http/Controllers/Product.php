<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with('category', 'user')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'expired_at' => 'required|date',
            'image' => 'requeired|image|'
        ]);

        $product = new Product($request->all());
        $product->modified_by = $request->user()->id;

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products');
        }

        $product->save();
        return $product;
    }

    public function show(Product $product)
    {
        return $product->load('category', 'user');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'expired_at' => 'required|date',
            'image' => 'nullable|image'
        ]);

        $product->update($request->all());
        $product->modified_by = $request->user()->id;

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products');
        }

        $product->save();
        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}
?>