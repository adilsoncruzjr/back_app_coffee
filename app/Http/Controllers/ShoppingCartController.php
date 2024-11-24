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
    // Validação
    $validated = $request->validate([
        'id_user' => 'required|integer|exists:users,id',
        'id_prod_q' => 'required|array', // Certifica-se de que é um array
        'id_prod_q.*' => 'string|exists:products,id_prod', // Valida cada item no array
        'final_value_car' => 'required|numeric|min:0',
    ]);

    // Log para verificar os dados recebidos
    Log::info('Recebendo dados para adicionar ao carrinho:', $validated);

    // Cria ou atualiza o carrinho
    $shoppingCart = ShoppingCart::create([
        'id_user' => $validated['id_user'],
        'id_prod_q' => $validated['id_prod_q'], // Armazena o array no banco
        'final_value_car' => $validated['final_value_car'],
    ]);

    // Retorna a resposta com o ID do carrinho
    return response()->json([
        'message' => 'Carrinho de compras salvo com sucesso!',
        'data' => $shoppingCart,
        'id' => $shoppingCart->id, // Retorna o id do carrinho criado
    ]);
}



    public function getProductIds($id)
{
    // Recupera o carrinho de compras pelo ID
    $cart = ShoppingCart::find($id);

    // Verifica se o carrinho foi encontrado
    if (!$cart) {
        return response()->json(['message' => 'Shopping cart not found.'], 404);
    }

    // Verifica o tipo de dados de id_prod_q
    if (is_string($cart->id_prod_q)) {
        // Se for uma string, tenta decodificar o JSON
        $productIds = json_decode($cart->id_prod_q, true);

        // Verifica se a decodificação foi bem-sucedida
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($productIds)) {
            return response()->json(['message' => 'Invalid product list format in shopping cart.'], 400);
        }
    } else {
        // Se for um array, usa diretamente
        $productIds = $cart->id_prod_q;
    }

    // Retorna os dados do carrinho e a lista de IDs de produtos
    return response()->json([
        'shopping_cart_id' => $cart->id,
        'id_user' => $cart->id_user,
        'final_value_car' => $cart->final_value_car,
        'id_prod_q' => $productIds, // Retorna como array de IDs de produtos
    ], 200);
}


}
