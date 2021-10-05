<?php

namespace App\Foodstock\Bridge\Neemo\Listeners;

use App\Foodstock\Bridge\Neemo\Events\PulledEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Foodstock\Bridge\Neemo\OrdersHandler;

class RequestAndProcessOrders implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PulledEvents  $event
     * @return void
     */
    public function handle(PulledEvents $event)
    {
        (new OrdersHandler($event->neemoBroker))->handle();
    }
}
