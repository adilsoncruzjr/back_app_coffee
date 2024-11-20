<?php

namespace App\Http\Controllers;

use App\Models\ShoppingCart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ShoppingCartController extends Controller
{
    /**
     * Adiciona uma lista de produtos ao carrinho de compras.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Logando a requisição recebida
        Log::info('Recebendo dados para adicionar ao carrinho:', $request->all());

        // Validando os dados recebidos
        try {
            $validatedData = $request->validate([
                'id_user' => 'required|exists:users,id', // Verifica se o usuário existe
                'id_prod_q' => 'required|array|min:1', // A lista de produtos deve ser um array
                'id_prod_q.*' => 'exists:products,id', // Verifica se cada produto na lista existe
                'final_value_car' => 'required|numeric|min:0', // Valor final do carrinho
            ]);
            Log::info('Dados validados com sucesso:', $validatedData); // Logando os dados validados
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro na validação dos dados:', $e->errors()); // Logando o erro de validação
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 400);
        }

        // Criando o novo item no carrinho
        try {
            $shoppingCart = ShoppingCart::create([
                'id_user' => $validatedData['id_user'],
                'id_prod_q' => json_encode($validatedData['id_prod_q']), // Armazenando a lista de IDs como JSON
                'final_value_car' => $validatedData['final_value_car'],
            ]);
            Log::info('Produto adicionado ao carrinho com sucesso:', $shoppingCart->toArray()); // Logando o carrinho criado
        } catch (\Exception $e) {
            Log::error('Erro ao criar o item no carrinho:', ['error' => $e->getMessage()]); // Logando erro ao criar carrinho
            return response()->json(['error' => 'Failed to add product to cart', 'details' => $e->getMessage()], 500);
        }

        // Retornando a resposta com os dados do item criado
        return response()->json([
            'message' => 'Products added to shopping cart successfully.',
            'shopping_cart' => $shoppingCart
        ], 201);
    }

    // Método para obter o valor de 'id_prod_q' de um carrinho
    public function getProductIds($id)
    {
        $cart = ShoppingCart::find($id);

        if (!$cart) {
            return response()->json(['message' => 'Shopping cart not found.'], 404);
        }

        return response()->json([
            'shopping_cart_id' => $cart->id,
            'id_prod_q' => json_decode($cart->id_prod_q) // Decodificando o JSON para retornar como array
        ], 200);
    }

    // Método para atualizar o valor de 'id_prod_q' de um carrinho
    public function updateProductIds(Request $request, $id)
    {
        $request->validate([
            'id_prod_q' => 'required|array|min:1', // A lista de produtos deve ser um array
            'id_prod_q.*' => 'exists:products,id', // Verifica se cada ID de produto existe
        ]);

        $cart = ShoppingCart::find($id);

        if (!$cart) {
            return response()->json(['message' => 'Shopping cart not found.'], 404);
        }

        // Atualizando o valor de 'id_prod_q' com a lista recebida
        $cart->id_prod_q = json_encode($request->id_prod_q); // Convertendo a lista para JSON
        $cart->save();

        return response()->json([
            'message' => 'Product list updated successfully.',
            'shopping_cart_id' => $cart->id,
            'new_id_prod_q' => json_decode($cart->id_prod_q) // Retornando a lista atualizada
        ], 200);
    }

    // Método para obter o valor de 'final_value_car' de um carrinho
    public function getFinalValue($id)
    {
        $cart = ShoppingCart::find($id);

        if (!$cart) {
            return response()->json(['message' => 'Shopping cart not found.'], 404);
        }

        return response()->json([
            'shopping_cart_id' => $cart->id,
            'final_value_car' => $cart->final_value_car
        ], 200);
    }

    // Método para atualizar o valor de 'final_value_car' de um carrinho
    public function updateFinalValue(Request $request, $id)
    {
        $request->validate([
            'final_value_car' => 'required|numeric|min:0', // O valor final do carrinho deve ser um número não negativo
        ]);

        $cart = ShoppingCart::find($id);

        if (!$cart) {
            return response()->json(['message' => 'Shopping cart not found.'], 404);
        }

        // Atualizando o valor de 'final_value_car'
        $cart->final_value_car = $request->final_value_car;
        $cart->save();

        return response()->json([
            'message' => 'Final value updated successfully.',
            'shopping_cart_id' => $cart->id,
            'new_final_value_car' => $cart->final_value_car
        ], 200);
    }
}
