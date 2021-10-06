<?php
namespace App\Foodstock\Bridge\Neemo;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\NeemoBroker;
use App\Models\NeemoOrder;
use App\Models\NeemoEvent;

use App\Foodstock\Integration\Backoffice\StartProduction;
use App\Foodstock\Integration\Backoffice\StartProductionAsync;
use App\Foodstock\Integration\Backoffice\StartProductionBody;
use App\Foodstock\Bridge\Neemo\BaseHandler;
use App\Foodstock\Bridge\Neemo\Events\StartedOrderProduction;
use App\Foodstock\Actions\RestartOrderProccess;

use Illuminate\Support\Facades\Log;

class StartProductionHandler extends BaseHandler{

    private NeemoEvent $neemoEvent;

    public function __construct(NeemoBroker $neemoBroker, NeemoEvent $neemoEvent){
        parent::__construct($neemoBroker);
        $this->neemoEvent = $neemoEvent;
        Log::info("NEEMO integration - Step FOUR : send order o backoffice system", ["restaurant_id", $neemoBroker->restaurant_id]);
    }
 
    public function handle(){
        try{
            $neemoOrder = NeemoOrder::where("orderId", $this->neemoEvent->orderId)
                //->where("processed", 1)
                //->where("started_production", 0)
                ->first();

            Log::info("NEEMO integration - Step FOUR FOUND ", ["order_id" => $this->neemoEvent->orderId]);

            $promises = [];

            $startProductionBodies = [];

            $startProduction = new StartProduction(env("BACKOFFICE_TOKEN"), 
                new StartProductionBody($this->neemoBroker->broker_id, $this->neemoBroker->restaurant_id, $neemoOrder)
            );
            
            //Abre pedido no backoffice

            $response = $startProduction->request(); //Abre pedido no backoffice

            if(is_object($response) && isset($response->success) && $response->success){
                $neemoOrder->started_production = 1;
            }else{
                $neemoOrder->started_production = 0;
            }
            
            $neemoOrder->save();            

            StartedOrderProduction::dispatch($this->neemoBroker); //Dá conhecimento
        }catch(\Exception $e){
            //Se não integrar, reinicia processo para tentar novamente
            RestartOrderProccess::restart($this->neemoEvent->orderId, 3); //TODO Use enums
        }
    }
}
