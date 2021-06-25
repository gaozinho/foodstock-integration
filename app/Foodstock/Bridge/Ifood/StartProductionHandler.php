<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodOrder;
use App\Models\IfoodEvent;

use App\Foodstock\Integration\Backoffice\StartProduction;
use App\Foodstock\Integration\Backoffice\StartProductionBody;
use App\Foodstock\Bridge\Ifood\BaseHandler;
use App\Foodstock\Bridge\Ifood\Events\StartedOrderProduction;

use Illuminate\Support\Facades\Log;

class StartProductionHandler extends BaseHandler{

    private IfoodEvent $ifoodEvent;

    public function __construct(IfoodBroker $ifoodBroker, IfoodEvent $ifoodEvent){
        parent::__construct($ifoodBroker);
        $this->ifoodEvent = $ifoodEvent;
        Log::info("IFOOD integration - Step FOUR : send order o backoffice system", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }
 
    public function handle(){
        $ifoodOrders = IfoodOrder::where("merchant_id", $this->ifoodBroker->merchant_id)
            ->where("processed", 1)
            ->where("started_production", 0)->get();
        foreach($ifoodOrders as $ifoodOrder){
            $startProduction = new StartProduction(env("BACKOFFICE_TOKEN"), 
                new StartProductionBody($this->ifoodBroker->broker_id, $this->ifoodBroker->restaurant_id, $ifoodOrder->orderId, $ifoodOrder->json)
            );

            $response = $startProduction->request(); //Abre pedido no backoffice

            if(is_object($response) && isset($response->success) && $response->success){
                $ifoodOrder->started_production = 1;
                $ifoodOrder->save();
            }

            StartedOrderProduction::dispatch($this->ifoodBroker); //Dá conhecimento

            return $response;
        }
    }
}
