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
     // Logando os dados recebidos
     Log::info('Recebendo dados para criar o pedido:', $request->all());

     // Validando os dados recebidos
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

     // Criando o novo pedido
     try {
         $order = Order::create([
             'id_user' => $validatedData['id_user'],
             'id_prod' => $request->input('id_prod') ? json_encode($request->input('id_prod')) : null, // Opcional
             'final_value' => $validatedData['final_value'],
             'status' => $validatedData['status'],
             'id_car' => $validatedData['id_car'],
         ]);
         Log::info('Pedido criado com sucesso:', $order->toArray());

         // Disparando o evento para atualizar o campo 'orders_id' do usuário
         event(new OrderCreated($order)); // Disparando o evento

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
     try {
         // Logando os dados recebidos
         Log::info('Iniciando a atualização do pedido. Dados recebidos:', $request->all());

         // Atualizando a validação para 'id_prod' não ser obrigatório
         $validatedData = $request->validate([
             'final_value' => 'nullable|numeric|min:0', // Agora o campo 'final_value' é opcional, mas deve ser um número não negativo
             'id_car' => 'required|exists:shopping_cart,id', // O ID do carrinho deve existir
             'id_user' => 'required|exists:users,id', // O ID do usuário deve existir
             'id_prod' => 'nullable|array', // 'id_prod' não é mais obrigatório, mas deve ser um array se presente
             'id_prod.*' => 'exists:products,id' // Verifica se cada ID de produto na lista existe (caso 'id_prod' seja fornecido)
         ]);

         // Logando os dados validados
         Log::info('Dados validados com sucesso:', $validatedData);

         // Buscando o pedido no banco de dados
         $order = Order::find($id);

         if (!$order) {
             Log::error('Pedido não encontrado. ID do pedido:', ['id' => $id]);
             return response()->json(['message' => 'Order not found.'], 404);
         } else {
             Log::info('Pedido encontrado. Iniciando atualização. Pedido atual:', $order->toArray());
         }

         // Atualizando as colunas 'final_value', 'id_car', 'id_user' e 'id_prod'
         if (isset($validatedData['final_value'])) {
             $order->final_value = $validatedData['final_value'];
             Log::info('Campo final_value atualizado:', ['final_value' => $validatedData['final_value']]);  // Agora passa um array
         } else {
             Log::info('Campo final_value não foi enviado. Mantendo valor atual.');
         }

         $order->id_car = $validatedData['id_car'];
         $order->id_user = $validatedData['id_user'];

         // Se 'id_prod' foi enviado, atualiza, caso contrário, mantém o valor atual
         if (isset($validatedData['id_prod'])) {
             $order->id_prod = json_encode($validatedData['id_prod']); // Armazenando 'id_prod' como JSON
             Log::info('Campo id_prod atualizado:', $validatedData['id_prod']);
         } else {
             Log::info('Campo id_prod não foi enviado. Mantendo valor atual.');
         }

         // Salvando o pedido atualizado
         $order->save();

         Log::info('Pedido atualizado com sucesso. Novo pedido:', $order->toArray());

         // Retornando a resposta com os dados atualizados do pedido
         return response()->json([
             'message' => 'Order updated successfully.',
             'order_id' => $order->id,
             'final_value' => $order->final_value,
             'id_car' => $order->id_car,
             'id_user' => $order->id_user,
             'id_prod' => json_decode($order->id_prod) // Exibindo 'id_prod' como array
         ], 200);

     } catch (\Illuminate\Validation\ValidationException $e) {
         // Logando erros de validação
         Log::error('Erro de validação:', $e->errors());
         return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
     } catch (\Exception $e) {
         // Logando qualquer erro inesperado
         Log::error('Erro inesperado durante a atualização do pedido:', ['error' => $e->getMessage()]);
         return response()->json(['message' => 'An unexpected error occurred.'], 500);
     }
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

 public function getUserOrders($id)
 {
     try {
         // Verificar se o usuário tem pedidos
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
