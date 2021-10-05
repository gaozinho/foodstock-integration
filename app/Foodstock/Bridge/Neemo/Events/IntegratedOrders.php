<?php

namespace App\Foodstock\Bridge\Neemo\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\NeemoBroker;
use App\Models\NeemoEvent;

class IntegratedOrders
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public NeemoBroker $neemoBroker;
    public NeemoEvent $neemoEvent;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(NeemoBroker $neemoBroker, NeemoEvent $neemoEvent)
    {
        $this->neemoBroker = $neemoBroker;
        $this->neemoEvent = $neemoEvent;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
