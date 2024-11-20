<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Criar um novo pedido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Logando os dados recebidos
        Log::info('Recebendo dados para criar o pedido:', $request->all());

        // Validando os dados recebidos
        $validatedData = $request->validate([
            'id_user' => 'required|exists:users,id', // Verifica se o usuário existe
            'id_prod' => 'required|array|min:1', // Lista de produtos deve ser um array
            'id_prod.*' => 'exists:products,id', // Verifica se cada produto existe
            'final_value' => 'required|numeric|min:0', // Valor final do pedido
            'status' => 'required|string', // Status do pedido
            'id_car' => 'required|exists:shopping_cart,id', // Verifica se o carrinho existe
        ]);

        // Criando o novo pedido
        $order = Order::create([
            'id_user' => $validatedData['id_user'],
            'id_prod' => json_encode($validatedData['id_prod']), // Lista de IDs de produtos como JSON
            'final_value' => $validatedData['final_value'],
            'status' => $validatedData['status'],
            'id_car' => $validatedData['id_car'],
        ]);

        // Logando o pedido criado
        Log::info('Pedido criado com sucesso:', $order->toArray());

        // Retornando a resposta com os dados do pedido
        return response()->json([
            'message' => 'Order created successfully.',
            'order' => $order
        ], 201);
    }

    // Método para obter os valores das colunas 'final_value', 'id_car', 'id_user' e 'id_prod'
    public function getOrderDetails($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json([
            'order_id' => $order->id,
            'final_value' => $order->final_value,
            'id_car' => $order->id_car,
            'id_user' => $order->id_user,
            'id_prod' => json_decode($order->id_prod) // 'id_prod' armazenado como JSON
        ], 200);
    }

    // Método para atualizar os valores de 'final_value', 'id_car', 'id_user' e 'id_prod'
    public function updateOrderDetails(Request $request, $id)
    {
        $request->validate([
            'final_value' => 'required|numeric|min:0', // O valor final deve ser um número não negativo
            'id_car' => 'required|exists:shopping_cart,id', // O ID do carrinho deve existir
            'id_user' => 'required|exists:users,id', // O ID do usuário deve existir
            'id_prod' => 'required|array|min:1', // A lista de produtos deve ser um array
            'id_prod.*' => 'exists:products,id' // Verifica se cada ID de produto na lista existe
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Atualizando as colunas 'final_value', 'id_car', 'id_user' e 'id_prod'
        $order->final_value = $request->final_value;
        $order->id_car = $request->id_car;
        $order->id_user = $request->id_user;
        $order->id_prod = json_encode($request->id_prod); // Armazenando 'id_prod' como JSON
        $order->save();

        return response()->json([
            'message' => 'Order updated successfully.',
            'order_id' => $order->id,
            'final_value' => $order->final_value,
            'id_car' => $order->id_car,
            'id_user' => $order->id_user,
            'id_prod' => json_decode($order->id_prod) // Exibindo 'id_prod' como array
        ], 200);
    }

    // Método para obter o valor da coluna 'status' de um pedido
    public function getOrderStatus($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json([
            'order_id' => $order->id,
            'status' => $order->status, // Retornando o valor do status
        ], 200);
    }

    // Método para atualizar o valor da coluna 'status' de um pedido
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|max:255', // O status deve ser uma string
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Atualizando a coluna 'status'
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order_id' => $order->id,
            'status' => $order->status,
        ], 200);
    }
}
