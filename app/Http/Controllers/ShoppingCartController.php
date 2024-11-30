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
        'id_prod_q' => 'required|array', 
        'id_prod_q.*' => 'string|exists:products,id_prod', 
        'final_value_car' => 'required|numeric|min:0',
    ]);


    Log::info('Recebendo dados para adicionar ao carrinho:', $validated);

    
    $shoppingCart = ShoppingCart::create([
        'id_user' => $validated['id_user'],
        'id_prod_q' => $validated['id_prod_q'], 
        'final_value_car' => $validated['final_value_car'],
    ]);

   
    return response()->json([
        'message' => 'Carrinho de compras salvo com sucesso!',
        'data' => $shoppingCart,
        'id' => $shoppingCart->id, 
    ]);
}



    public function getProductIds($id)
{
    
    $cart = ShoppingCart::find($id);

    
    if (!$cart) {
        return response()->json(['message' => 'Shopping cart not found.'], 404);
    }

    
    if (is_string($cart->id_prod_q)) {
       
        $productIds = json_decode($cart->id_prod_q, true);

     
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($productIds)) {
            return response()->json(['message' => 'Invalid product list format in shopping cart.'], 400);
        }
    } else {
        
        $productIds = $cart->id_prod_q;
    }

   
    return response()->json([
        'shopping_cart_id' => $cart->id,
        'id_user' => $cart->id_user,
        'final_value_car' => $cart->final_value_car,
        'id_prod_q' => $productIds, 
    ], 200);
}


}
