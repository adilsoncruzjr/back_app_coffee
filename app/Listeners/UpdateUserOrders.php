<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUserOrders
{
    public function handle(OrderCreated $event)
    {
        // Adicionar log no início para verificar se o listener foi chamado
        Log::info('Dentro do listener UpdateUserOrders para o pedido:', ['order_id' => $event->order->id]);

        $order = $event->order;
        $user = User::find($order->id_user);

        if ($user) {
            Log::info('Usuário encontrado para atualizar a coluna orders_id.', ['user_id' => $user->id]);

            // Obter os IDs das ordens existentes e adicionar a nova ordem
            $orders = $user->orders()->pluck('id')->toArray();
            Log::info('Ordens atuais do usuário:', ['orders' => $orders]);

            // Adicionando a nova ordem à lista de ordens
            $orders[] = $order->id;

            // Atualizando a coluna orders_id com a lista de IDs
            $user->orders_id = json_encode($orders);
            $user->save();

            Log::info('Coluna orders_id do usuário atualizada com sucesso.', ['orders_id' => $user->orders_id]);
        } else {
            Log::error('Usuário não encontrado para atualização da ordem.', ['user_id' => $order->id_user]);
        }
    }
}
