<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderCreated

{
    use Dispatchable, SerializesModels;

    public $order;

    // O construtor agora recebe um objeto Order
    public function __construct(Order $order)
    {
        $this->order = $order;  // Atribuindo o pedido recebido à propriedade $order
        Log::info('OrderCreated event triggered for order:', ['order_id' => $order->id]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('order.created.' . $this->order->id), // Channel privado específico para a ordem
        ];
    }
}
