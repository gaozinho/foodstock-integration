<?php

namespace App\Foodstock\Bridge\Ifood\Listeners;

use App\Foodstock\Bridge\Ifood\Events\IntegratedOrders;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Foodstock\Bridge\Ifood\OrderConfirmActionHandler;

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
        (new OrderConfirmActionHandler($event->ifoodBroker, $event->ifoodEvent))->handle();
        
    }
}
