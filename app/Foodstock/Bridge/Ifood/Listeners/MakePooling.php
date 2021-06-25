<?php

namespace App\Foodstock\Bridge\Ifood\Listeners;

use App\Foodstock\Bridge\Ifood\Events\StartedProccess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Foodstock\Bridge\Ifood\EventsHandler;


class MakePooling implements ShouldQueue
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
     * @param  StartedProccess  $event
     * @return void
     */
    public function handle(StartedProccess $event)
    {
        (new EventsHandler($event->ifoodBroker))->handle();
    }
}
