<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BuyerEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
//    public  $product_id;
//    public  $order_status;
//    public $delivering_date;
//
//    /**
//     * Create a new event instance.
//     *
//     * @return void
//     */
//    public function __construct($product_id,$order_status,$delivering_date)
//    {
//        $this->product_id=$product_id;
//        $this->order_status=$order_status;
//        $this->delivering_date=$delivering_date;
//
//    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['buyer-channel'];
    }

    public  function  broadcastAs(){
        return "buyer-event";
    }
}
