<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Order\OrderDetail;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodEvent;
use App\Models\IfoodOrder;

use App\Foodstock\Bridge\Ifood\Events\CanceledOrders;
use App\Foodstock\Bridge\Ifood\Events\IntegratedOrders;
use App\Foodstock\Bridge\Ifood\BaseHandler;

use Illuminate\Support\Facades\Log;

class OrdersHandler extends BaseHandler{

    public function __construct(IfoodBroker $ifoodBroker){
        parent::__construct($ifoodBroker);
        Log::info("IFOOD integration - Step TWO : get order detail", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }

    public function handle(){
        $ifoodOrders = [];
        $ifoodEvents = IfoodEvent::where("merchant_id", $this->ifoodBroker->merchant_id)
            ->where("processed", 0)->get();

        foreach($ifoodEvents as $ifoodEvent){

            if($ifoodEvent->code == "PLC"){
                $orderDetail = new OrderDetail($this->ifoodBroker->accessToken, $ifoodEvent->orderId);
                $orderJson = $orderDetail->request();
                
                try{
                    $ifoodOrders[] = IfoodOrder::updateOrCreate(
                    [
                        'orderId' => $ifoodEvent->orderId
                    ],
                    [
                        'ifood_event_id' => $ifoodEvent->id, 
                        'merchant_id' => $ifoodEvent->merchant_id, 
                        'json' => json_encode($orderJson) , 
                        'processed' => 0
                    ]); 
                    
                    //Tira o evento da lista
                    $ifoodEvent->processed = 1;
                    $ifoodEvent->processed_at = date("Y-m-d H:i:s");
                    $ifoodEvent->save();
                }catch(\Exception $e){
                    //TODO - Tratar chave duplicada
                }
                
                //PEGA DETALHES DO PEDIDO
                IntegratedOrders::dispatch($this->ifoodBroker, $ifoodEvent); //Aceita o pedido no ifood

            }elseif($ifoodEvent->code == "CAN"){
                //Tira o evento da lista
                $ifoodEvent->processed = 1;
                $ifoodEvent->processed_at = date("Y-m-d H:i:s");
                $ifoodEvent->save();         

                //CANCELA PEDIDO
                CanceledOrders::dispatch($this->ifoodBroker, $ifoodEvent);

       
            }
        }
        
        //return $ifoodOrders;
    }
   
}
