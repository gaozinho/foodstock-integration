<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodOrder;
use App\Models\IfoodEvent;

use App\Foodstock\Integration\Backoffice\CancelProduction;
use App\Foodstock\Integration\Backoffice\CancelProductionBody;
use App\Foodstock\Bridge\Ifood\BaseHandler;
use App\Foodstock\Bridge\Ifood\Events\StartedOrderProduction;

use Illuminate\Support\Facades\Log;

class CancelProductionHandler extends BaseHandler{

    private IfoodEvent $ifoodEvent;

    public function __construct(IfoodBroker $ifoodBroker, IfoodEvent $ifoodEvent){
        parent::__construct($ifoodBroker);
        $this->ifoodEvent = $ifoodEvent;
        Log::info("IFOOD integration - Step CANCEL : Backoffice : send order o backoffice system", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }
 
    public function handle(){
        
        $ifoodOrders = IfoodOrder::where("orderId", $this->ifoodEvent->orderId)
            ->get();
//dd($this->ifoodEvent);
        foreach($ifoodOrders as $ifoodOrder){

            $ifoodCancellation = json_decode($this->ifoodEvent->json);
            $code = isset($ifoodCancellation->metadata) ? $ifoodCancellation->metadata->CANCEL_CODE : 0;
            $reason = isset($ifoodCancellation->metadata) ? $ifoodCancellation->metadata->CANCEL_REASON : "Undefined";
            $origin = isset($ifoodCancellation->metadata) ? $ifoodCancellation->metadata->CANCEL_ORIGIN : "Undefined";

            $cancelProduction = new CancelProduction(env("BACKOFFICE_TOKEN"), 
                new CancelProductionBody($this->ifoodBroker->broker_id, $this->ifoodBroker->restaurant_id, $ifoodOrder->orderId, $this->ifoodEvent->json, $reason, $code, $origin)
            );

            $response = $cancelProduction->request(); //Envia fato para o backoffice

            if(is_object($response) && isset($response->success) && $response->success){

                //Tira o evento da lista
                $this->ifoodEvent->processed = 1;
                $this->ifoodEvent->processed_at = date("Y-m-d H:i:s");
                $this->ifoodEvent->save();

                $ifoodOrder->canceled_production = 1;
                $ifoodOrder->save();
            }

            //return $response;
        }
    }
}
