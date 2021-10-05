<?php
namespace App\Foodstock\Bridge\Neemo;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\NeemoBroker;
use App\Models\NeemoOrder;
use App\Models\NeemoEvent;
use App\Foodstock\Integration\Neemo\Enums\EndPoints;

use App\Foodstock\Integration\Neemo\Order\OrderAction;
use App\Foodstock\Integration\Neemo\Enums\OrderStatus;
use App\Foodstock\Integration\Neemo\RequestParameters\PoolingParameters;
use App\Foodstock\Bridge\Neemo\BaseHandler;
use Illuminate\Support\Facades\Log;


class OrderDispatchActionHandler extends BaseHandler{

    private NeemoEvent $neemoEvent;

    public function __construct(NeemoBroker $neemoBroker, NeemoEvent $neemoEvent){
        parent::__construct($neemoBroker);
        $this->neemoEvent = $neemoEvent;
        Log::info("NEEMO integration - Step SIX : dispatch order integration", ["restaurant_id", $neemoBroker->restaurant_id]);
    }
 
    public function handle(){

        $success = false;

        $parameters = new PoolingParameters($this->neemoBroker->accessToken, OrderStatus::Enviado);
        $orderAction = new OrderAction($parameters, $this->neemoEvent->orderId);        

        $success = $orderAction->request(); //Dá conhecimento

        $neemoOrder = NeemoOrder::where("orderId", $this->neemoEvent->orderId)->firstOrFail(); 
        if($success){
            $neemoOrder->dispatched = 1;
            $neemoOrder->save();
        }

        return $success;

    }
}
