<?php
namespace App\Foodstock\Bridge\Neemo;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Neemo\Order\Events\Pooling;
use App\Foodstock\Integration\Neemo\RequestParameters\PoolingParameters;

use App\Models\Broker;
use App\Models\NeemoBroker;
use App\Models\NeemoEvent;

use App\Foodstock\Bridge\Neemo\Events\PulledEvents;
use App\Foodstock\Bridge\Neemo\BaseHandler;
use Illuminate\Support\Facades\Log;

class EventsHandler extends BaseHandler{

    public function __construct(NeemoBroker $neemoBroker){
        parent::__construct($neemoBroker);
        Log::info("NEEMO integration - Step ONE : pooling", ["restaurant_id", $neemoBroker->restaurant_id]);
    }
 
    public function handle(){
        $parameters = new PoolingParameters($this->neemoBroker->accessToken, 0, 50, 1, null, "", date("Y-m-d H:i:s"));
        //$parameters = new PoolingParameters($this->neemoBroker->accessToken, 1, 2);
        
        $pooling = new Pooling($parameters);

        $poolingsJson = $pooling->request(); //TODO - Tratar token expirados

        $pages = intdiv($poolingsJson->paging->total, $poolingsJson->paging->limit) + 1;

        Log::info("NEEMO integration - FOUND ON POOLING: " . (is_array($poolingsJson) ? count($poolingsJson) : 0));

        $neemoEvents = [];

        //Passa por todas as páginas
        //for($i = 0; $i < $pages; $i++){
            if(isset($poolingsJson->Orders) && is_array($poolingsJson->Orders)){
                foreach($poolingsJson->Orders as $poolingJson){
                    $neemoEvents[] = NeemoEvent::updateOrCreate(
                    [
                        'id' => $poolingJson->id,
                        'merchant_id' => $this->neemoBroker->id
                    ],
                    [
                        'id' => $poolingJson->id, 
                        'merchant_id' => $this->neemoBroker->id, 
                        'createdAt' =>  date("Y-m-d H:i:s", strtotime($poolingJson->date)), 
                        'fullCode' => $poolingJson->status, 
                        'code' => $poolingJson->status, 
                        'orderId' => $poolingJson->id, 
                        'json' => json_encode($poolingJson) , 
                        'processed' => 0
                    ]);
                    Log::info("NEEMO integration - " . $poolingJson->id);
                }
            }
        //}


        PulledEvents::dispatch($this->neemoBroker); //Dá conhecimento ao neemo e recupera detalhes do pedido

        return $neemoEvents;
    }
}
