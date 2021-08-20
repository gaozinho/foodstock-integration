<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Order\Events\Pooling;
use App\Foodstock\Integration\Ifood\RequestParameters\QueryStringParameters;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodEvent;

use App\Foodstock\Bridge\Ifood\Events\PulledEvents;
use App\Foodstock\Bridge\Ifood\BaseHandler;
use Illuminate\Support\Facades\Log;

class EventsHandler extends BaseHandler{

    public function __construct(IfoodBroker $ifoodBroker){
        parent::__construct($ifoodBroker);
        Log::info("IFOOD integration - Step ONE : pooling", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }
 
    public function handle(){
        
        $pooling = new Pooling($this->ifoodBroker->accessToken, new QueryStringParameters(["types" => "PLC,CAN"]));
        $poolingsJson = $pooling->request(); //TODO - Tratar token expirados

        
        Log::info("IFOOD integration - FOUND ON POOLING: " . (is_array($poolingsJson) ? count($poolingsJson) : 0));
        //Log::info("IFOOD integration - POOLING DATA: " . (is_array($poolingsJson) ? print_r($poolingsJson, true) : ""));

        //Log::info("IFOOD integration - EVENTS: " . print_r($poolingsJson,true));

        $ifoodEvents = [];
        //dd($poolingsJson);
        
        if(is_array($poolingsJson)){
            foreach($poolingsJson as $poolingJson){
                try{

                    $ifoodEvents[] = IfoodEvent::create([
                        'id' => $poolingJson->id, 
                        'merchant_id' => $this->ifoodBroker->merchant_id, 
                        'createdAt' =>  date("Y-m-d H:i:s", strtotime($poolingJson->createdAt)), 
                        'fullCode' => $poolingJson->fullCode, 
                        'code' => $poolingJson->code, 
                        'orderId' => $poolingJson->orderId, 
                        'json' => json_encode($poolingJson) , 
                        'processed' => 0
                    ]);
                    Log::info("IFOOD integration - ORDERID: " . $poolingJson->orderId);

                }catch(\Exception $e){
                    //TODO - Tratar chave duplicada
                }
            }
        }

        PulledEvents::dispatch($this->ifoodBroker); //Dá conhecimento ao ifood e recupera detalhes do pedido

        //return $ifoodEvents;
    }
}
