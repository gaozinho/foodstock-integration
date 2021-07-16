<?php
namespace App\Foodstock\Integration\Backoffice;

use App\Foodstock\Integration\Ifood\Enums\GrantType;

class CancelProductionBody{

    private $broker_id;
    private $restaurant_id;
    private $order_id;
    private $order_json;
    private $event_json;

    public function __construct($broker_id, $restaurant_id, $order_id, $order_json, $event_json)
    {
        $this->broker_id = $broker_id;
        $this->restaurant_id = $restaurant_id;
        $this->order_id = $order_id;
        $this->order_json = $order_json;
        $this->event_json = $event_json;
    }

    public function toArray(){
        return [
            "broker_id" => $this->broker_id,
            "restaurant_id" => $this->restaurant_id,
            "order_id" => $this->order_id,
            "order_json" => $this->order_json,
            "event_json" => $this->event_json,
        ];
    }

}