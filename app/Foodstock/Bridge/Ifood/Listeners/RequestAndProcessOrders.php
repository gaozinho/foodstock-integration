<?php

namespace App\Foodstock\Bridge\Ifood\Listeners;

use App\Foodstock\Bridge\Ifood\Events\PulledEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Foodstock\Bridge\Ifood\OrdersHandler;

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
        (new OrdersHandler($event->ifoodBroker))->handle();
    }
}
