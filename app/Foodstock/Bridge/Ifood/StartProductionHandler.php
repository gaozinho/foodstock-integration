<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodOrder;
use App\Models\IfoodEvent;

use App\Foodstock\Integration\Backoffice\StartProduction;
use App\Foodstock\Integration\Backoffice\StartProductionAsync;
use App\Foodstock\Integration\Backoffice\StartProductionBody;
use App\Foodstock\Bridge\Ifood\BaseHandler;
use App\Foodstock\Bridge\Ifood\Events\StartedOrderProduction;
use App\Foodstock\Actions\RestartOrderProccess;

use Illuminate\Support\Facades\Log;


class StartProductionHandler extends BaseHandler{

    private IfoodEvent $ifoodEvent;

    public function __construct(IfoodBroker $ifoodBroker, IfoodEvent $ifoodEvent){
        parent::__construct($ifoodBroker);
        $this->ifoodEvent = $ifoodEvent;
        Log::info("IFOOD integration - Step FOUR : send order o backoffice system", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }
 
    public function handle(){
        try{
            $ifoodOrder = IfoodOrder::where("orderId", $this->ifoodEvent->orderId)
                //->where("processed", 1)
                //->where("started_production", 0)
                ->first();

            Log::info("IFOOD integration - Step FOUR FOUND ", ["order_id" => $this->ifoodEvent->orderId]);

            $promises = [];

            $startProductionBodies = [];


            //foreach($ifoodOrders as $ifoodOrder){
                //Log::info("IFOOD integration - Step FOUR", ["Restaurant ID" => $this->ifoodBroker->restaurant_id, "Order ID", $ifoodOrder->orderId]);
                
                //$startProductionBodies[] = new StartProductionBody($this->ifoodBroker->broker_id, $this->ifoodBroker->restaurant_id, $ifoodOrder);
                
                
                $startProduction = new StartProduction(env("BACKOFFICE_TOKEN"), 
                    new StartProductionBody($this->ifoodBroker->broker_id, $this->ifoodBroker->restaurant_id, $ifoodOrder)
                );
                
                //Abre pedido no backoffice

                
                $response = $startProduction->request(); //Abre pedido no backoffice

                if(is_object($response) && isset($response->success) && $response->success){
                    $ifoodOrder->started_production = 1;
                    $ifoodOrder->save();
                }
                

                $ifoodOrder->started_production = 1;
                $ifoodOrder->save();            
                
                //return $response;
            //}     

            //$startProduction = new StartProductionAsync(env("BACKOFFICE_TOKEN"), $startProductionBodies);        
            //$startProduction->request();
                
            StartedOrderProduction::dispatch($this->ifoodBroker); //Dá conhecimento
        }catch(\Exception $e){
            //Se não integrar, reinicia processo para tentar novamente
            RestartOrderProccess::restart($this->neemoEvent->orderId, 1); //TODO Use enums
        }            
    }
}
