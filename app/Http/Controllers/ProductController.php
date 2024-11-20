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
        // Validando os dados do produto
        $validatedData = $request->validate([
            'name_prod' => 'required|string|max:255',
            'value_prod' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'stock' => 'required|integer|min:0',
        ]);

        // Criando o novo produto
        $product = Product::create($validatedData);
        

        // Retornando a resposta com os dados do produto criado
        return response()->json([
            'message' => 'Product created successfully.',
            'product' => $product
        ], 201);
    }

    public function index(Request $request)
{
    // Obtém o número de itens por página do request ou usa um valor padrão (6)
    $perPage = $request->input('per_page', 6);

    // Realiza a paginação dos produtos
    $products = Product::paginate($perPage);

    // Retorna a resposta paginada
    return response()->json([
        'message' => 'Products retrieved successfully.',
        'products' => $products
    ], 200);
}

public function show($id_prod)
{
    // Busca o produto pelo id_prod (campo id_prod da tabela)
    $product = Product::where('id_prod', $id_prod)->first();

    if (!$product) {
        return response()->json(['message' => 'Product not found.'], 404);
    }

    return response()->json([
        'message' => 'Product retrieved successfully.',
        'product' => $product
    ], 200);
}

// Método para obter o valor de 'stock' de um produto
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

    // Método para atualizar o valor de 'stock' de um produto
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0', // O novo valor de 'stock' deve ser um inteiro maior ou igual a 0
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
