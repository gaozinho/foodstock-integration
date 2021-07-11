<?php

namespace App\Foodstock\Bridge\Ifood\Listeners;

use App\Foodstock\Bridge\Ifood\Events\CanceledOrders;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Foodstock\Bridge\Ifood\CancelProductionHandler;
use Illuminate\Support\Facades\Log;

class CancelProduction implements ShouldQueue
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
    public function handle(CanceledOrders $event)
    {
        (new CancelProductionHandler($event->ifoodBroker, $event->ifoodEvent))->handle();
    }
}
