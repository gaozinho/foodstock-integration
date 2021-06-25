<?php
namespace App\Foodstock\Integration\Backoffice;

use App\Foodstock\Integration\Ifood\Enums\GrantType;

class StartProductionBody{

    private $broker_id;
    private $restaurant_id;
    private $order_id;
    private $json;

    public function __construct($broker_id, $restaurant_id, $order_id, $json)
    {
        $this->broker_id = $broker_id;
        $this->restaurant_id = $restaurant_id;
        $this->order_id = $order_id;
        $this->json = $json;
    }

    public function toArray(){
        return [
            "broker_id" => $this->broker_id,
            "restaurant_id" => $this->restaurant_id,
            "order_id" => $this->order_id,
            "json" => $this->json,
        ];
    }

}