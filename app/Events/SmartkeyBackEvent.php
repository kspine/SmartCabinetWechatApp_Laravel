<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SmartkeyBackEvent extends Event
{
    use SerializesModels;

    public $client_ids;
    public $sn;
    public $door;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($subscribers,$sn,$door)
    {
        $this->client_ids = $subscribers;
        $this->sn = $sn;
        $this->door = $door;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
