<?php

namespace App\Foodstock\Bridge\Ifood\Listeners;

use App\Foodstock\Bridge\Ifood\Events\StartedOrderProduction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Foodstock\Bridge\Ifood\AcknowledgmentsHandler;

class SendAcknowledgments implements ShouldQueue
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
    public function handle(StartedOrderProduction $event)
    {
        (new AcknowledgmentsHandler($event->ifoodBroker))->handle();
    }
}
