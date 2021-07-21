<?php
namespace App\Foodstock\Integration\Backoffice;

use App\Foodstock\Integration\Ifood\Enums\GrantType;

class CancelProductionBody{

    private $broker_id;
    private $restaurant_id;
    private $order_id;
    private $event_json;
    private $reason;
    private $code;
    private $origin;

    public function __construct($broker_id, $restaurant_id, $order_id, $event_json, $reason, $code, $origin)
    {
        $this->broker_id = $broker_id;
        $this->restaurant_id = $restaurant_id;
        $this->order_id = $order_id;
        $this->event_json = $event_json;
        $this->reason = $reason;
        $this->code = $code;
        $this->origin = $origin;
    }

    public function toArray(){
        return [
            "broker_id" => $this->broker_id,
            "restaurant_id" => $this->restaurant_id,
            "order_id" => $this->order_id,
            "event_json" => $this->event_json,
            "reason" => $this->reason,
            "code" => $this->code,
            "origin" => $this->origin,
        ];
    }

}