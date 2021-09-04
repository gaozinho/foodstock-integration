<?php

namespace App\Foodstock\Bridge\Ifood\Listeners;

use App\Foodstock\Bridge\Ifood\Events\ConcludedOrders;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Foodstock\Bridge\Ifood\ConcludedProductionHandler;
use Illuminate\Support\Facades\Log;

class ConcludedProduction implements ShouldQueue
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
    public function handle(ConcludedOrders $event)
    {
        (new ConcludedProductionHandler($event->ifoodBroker, $event->ifoodEvent))->handle();
    }
}
