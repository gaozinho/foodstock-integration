<?php

namespace App\Foodstock\Bridge\Neemo\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\IfoodBroker;

class StartedOrderProduction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ifoodBroker;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(IfoodBroker $ifoodBroker)
    {
        $this->ifoodBroker = $ifoodBroker;
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
