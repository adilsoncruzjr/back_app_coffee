<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\OrderCreated;

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

        Log::info('Recebendo dados para criar o pedido:', $request->all());


        try {
            $validatedData = $request->validate([
                'id_user' => 'required|exists:users,id', // Verifica se o usuário existe
                'final_value' => 'required|numeric|min:0', // Valor final do pedido
                'status' => 'required|string', // Status do pedido
                'id_car' => 'required|exists:shopping_cart,id', // Verifica se o carrinho existe
            ]);
            Log::info('Dados validados com sucesso.', $validatedData);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro na validação dos dados.', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }


        try {
            $order = Order::create([
                'id_user' => $validatedData['id_user'],
                'id_prod' => $request->input('id_prod') ? json_encode($request->input('id_prod')) : null, // Opcional
                'final_value' => $validatedData['final_value'],
                'status' => $validatedData['status'],
                'id_car' => $validatedData['id_car'],
            ]);
            Log::info('Pedido criado com sucesso:', $order->toArray());


            event(new OrderCreated($order));
        } catch (\Exception $e) {
            Log::error('Erro ao criar o pedido.', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to create order.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Order created successfully.',
            'order' => $order
        ], 201);
    }


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
            'id_prod' => json_decode($order->id_prod)
        ], 200);
    }


    public function updateOrderDetails(Request $request, $id)
    {
        try {

            Log::info('Iniciando a atualização do pedido. Dados recebidos:', $request->all());


            $validatedData = $request->validate([
                'final_value' => 'nullable|numeric|min:0',
                'id_car' => 'required|exists:shopping_cart,id',
                'id_user' => 'required|exists:users,id',
                'id_prod' => 'nullable|array',
                'id_prod.*' => 'exists:products,id'
            ]);


            Log::info('Dados validados com sucesso:', $validatedData);


            $order = Order::find($id);

            if (!$order) {
                Log::error('Pedido não encontrado. ID do pedido:', ['id' => $id]);
                return response()->json(['message' => 'Order not found.'], 404);
            } else {
                Log::info('Pedido encontrado. Iniciando atualização. Pedido atual:', $order->toArray());
            }


            if (isset($validatedData['final_value'])) {
                $order->final_value = $validatedData['final_value'];
                Log::info('Campo final_value atualizado:', ['final_value' => $validatedData['final_value']]);
            } else {
                Log::info('Campo final_value não foi enviado. Mantendo valor atual.');
            }

            $order->id_car = $validatedData['id_car'];
            $order->id_user = $validatedData['id_user'];


            if (isset($validatedData['id_prod'])) {
                $order->id_prod = json_encode($validatedData['id_prod']);
                Log::info('Campo id_prod atualizado:', $validatedData['id_prod']);
            } else {
                Log::info('Campo id_prod não foi enviado. Mantendo valor atual.');
            }


            $order->save();

            Log::info('Pedido atualizado com sucesso. Novo pedido:', $order->toArray());


            return response()->json([
                'message' => 'Order updated successfully.',
                'order_id' => $order->id,
                'final_value' => $order->final_value,
                'id_car' => $order->id_car,
                'id_user' => $order->id_user,
                'id_prod' => json_decode($order->id_prod)
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::error('Erro de validação:', $e->errors());
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {

            Log::error('Erro inesperado durante a atualização do pedido:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }


    public function getOrderStatus($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json([
            'order_id' => $order->id,
            'status' => $order->status,
        ], 200);
    }


    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }


        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order_id' => $order->id,
            'status' => $order->status,
        ], 200);
    }

    public function getUserOrders($id)
    {
        try {

            $orders = Order::where('id_user', $id)->get(['id', 'final_value', 'status', 'id_car']);

            if ($orders->isEmpty()) {
                return response()->json(['message' => 'No orders found for this user.'], 404);
            }

            return response()->json(['orders' => $orders], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar pedidos do usuário:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
