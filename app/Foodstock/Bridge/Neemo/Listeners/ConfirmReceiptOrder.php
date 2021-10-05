<?php

namespace App\Foodstock\Bridge\Neemo\Listeners;

use App\Foodstock\Bridge\Neemo\Events\IntegratedOrders;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Foodstock\Bridge\Neemo\OrderConfirmActionHandler;

class ConfirmReceiptOrder implements ShouldQueue
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
     * @param  IntegratedOrders  $event
     * @return void
     */
    public function handle(IntegratedOrders $event)
    {
        (new OrderConfirmActionHandler($event->neemoBroker, $event->neemoEvent))->handle();
        
    }
}
