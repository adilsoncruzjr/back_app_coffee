<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Adiciona um novo produto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'name_prod' => 'required|string|max:255',
            'value_prod' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'stock' => 'required|integer|min:0',
        ]);

        
        $product = Product::create($validatedData);
        

        
        return response()->json([
            'message' => 'Product created successfully.',
            'product' => $product
        ], 201);
    }

    public function index(Request $request)
{
    
    $perPage = $request->input('per_page', 6);

    
    $products = Product::paginate($perPage);

    
    return response()->json([
        'message' => 'Products retrieved successfully.',
        'products' => $products
    ], 200);
}

public function show($id_prod)
{
    
    $product = Product::where('id_prod', $id_prod)->first();

    if (!$product) {
        return response()->json(['message' => 'Product not found.'], 404);
    }

    return response()->json([
        'message' => 'Product retrieved successfully.',
        'product' => $product
    ], 200);
}


public function getStock($id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found.'], 404);
    }

    return response()->json([
        'product_id' => $product->id,
        'stock' => $product->stock
    ], 200);
}

    
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0', 
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->stock = $request->stock;
        $product->save();

        return response()->json([
            'message' => 'Stock updated successfully.',
            'product_id' => $product->id,
            'new_stock' => $product->stock
        ], 200);
    }
}
