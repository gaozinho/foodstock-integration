<?php

namespace App\Foodstock\Bridge\Ifood\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\IfoodBroker;
use App\Models\IfoodEvent;

class IntegratedOrders
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public IfoodBroker $ifoodBroker;
    public IfoodEvent $ifoodEvent;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(IfoodBroker $ifoodBroker, IfoodEvent $ifoodEvent)
    {
        $this->ifoodBroker = $ifoodBroker;
        $this->ifoodEvent = $ifoodEvent;
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
