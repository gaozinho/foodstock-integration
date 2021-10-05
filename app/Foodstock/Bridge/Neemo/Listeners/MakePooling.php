<?php

namespace App\Foodstock\Bridge\Neemo\Listeners;

use App\Foodstock\Bridge\Neemo\Events\StartedProccess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Foodstock\Bridge\Neemo\EventsHandler;


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
        (new EventsHandler($event->neemoBroker))->handle();
    }
}
