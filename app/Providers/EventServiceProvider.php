<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Events\OrderCreated;
use App\Listeners\UpdateUserOrders;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Listeners\SendLoginResponse;
use Illuminate\Broadcasting\Channel;

class EventServiceProvider extends ServiceProvider
{
    /**
     * O mapeamento dos eventos e seus listeners.
     *
     * @var array
     */
    protected $listen = [
        OrderCreated::class => [
            UpdateUserOrders::class,
        ],
    ];

    /**
     * Registrar quaisquer serviços de eventos.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
