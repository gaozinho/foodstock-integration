<?php
namespace App\Foodstock\Bridge\Neemo;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\NeemoBroker;
use App\Models\NeemoOrder;
use App\Models\NeemoEvent;
use App\Foodstock\Integration\Neemo\Enums\EndPoints;

use App\Foodstock\Integration\Neemo\Order\OrderAction;
use App\Foodstock\Bridge\Neemo\BaseHandler;
use Illuminate\Support\Facades\Log;
use App\Foodstock\Integration\Neemo\RequestParameters\PoolingParameters;
use App\Foodstock\Integration\Neemo\Enums\OrderStatus;


class OrderConfirmActionHandler extends BaseHandler{

    private NeemoEvent $neemoEvent;

    public function __construct(NeemoBroker $neemoBroker, NeemoEvent $neemoEvent){
        parent::__construct($neemoBroker);
        $this->neemoEvent = $neemoEvent;
        Log::info("NEEMO integration - Step THREE : confim order integration", ["restaurant_id", $neemoBroker->restaurant_id]);
    }
 
    public function handle(){
        $neemoOrders = NeemoOrder::where("orderId", $this->neemoEvent->orderId)
            ->where("processed", 0)->get();
        
        foreach($neemoOrders as $neemoOrder){
            $parameters = new PoolingParameters($this->neemoBroker->accessToken, OrderStatus::Confirmado);
            $orderAction = new OrderAction($parameters, $this->neemoEvent->orderId);
            $success = $orderAction->request(); //Avisa que aceitou o pedido
            $neemoOrder->processed = 1;
            $neemoOrder->save();
        }
    }
}
