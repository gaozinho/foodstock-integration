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
            ->where("processed", 0)
            ->where("processing", 0)
            ->get();

        Log::info("IFOOD integration - Step TWO TOTAL", ["Total", count($ifoodEvents)]);

        
        for($i = 0; $i< count($ifoodEvents); $i++){
            $ifoodEvents[$i]->processing = 1;
            $ifoodEvents[$i]->save();
        } 

        foreach($ifoodEvents as $ifoodEvent){

            if($ifoodEvent->code == "PLC"){
                $orderDetail = new OrderDetail($this->ifoodBroker->accessToken, $ifoodEvent->orderId);
                //Log::info("IFOOD integration - Step TWO", ["Order ID", $ifoodEvent->orderId]);
                $orderJson = $orderDetail->request();
                
                //$ifoodOrderMerchant = IfoodOrder::where("orderId", $ifoodEvent->orderId)->selectRaw("id, JSON_VALUE(JSON, '$.merchant.id') AS merchant_id")->first();
                
                try{

                    $ifoodOrder = IfoodOrder::where("orderId", $ifoodEvent->orderId)->first();
                    if(is_object($ifoodOrder)){ //Coloca o merchant correto, caso já exista
                        $ifoodOrderMerchant = IfoodOrder::where("orderId", $ifoodEvent->orderId)->selectRaw("id, JSON_VALUE(JSON, '$.merchant.id') AS merchant_id")->first();
                        $ifoodOrder->update([
                            'merchant_id' => $ifoodOrderMerchant ? $ifoodOrderMerchant->merchant_id : $ifoodEvent->merchant_id, //TODO - Validar merchant do JSON recebido no order
                        ]);
                        $ifoodOrders[] = $ifoodOrder;
                    }else{
                        //Cria e depois atualiza o merchant
                        $ifoodOrder = IfoodOrder::create(
                            [
                                'orderId' => $ifoodEvent->orderId,
                                'ifood_event_id' => $ifoodEvent->id, 
                                'merchant_id' => $ifoodEvent->merchant_id,
                                'json' => json_encode($orderJson) , 
                                'processed' => 0
                            ]); 
                        $ifoodOrderMerchant = IfoodOrder::where("orderId", $ifoodEvent->orderId)->selectRaw("id, JSON_VALUE(JSON, '$.merchant.id') AS merchant_id")->first();
                        $ifoodOrder->update([
                            'merchant_id' => $ifoodOrderMerchant ? $ifoodOrderMerchant->merchant_id : $ifoodEvent->merchant_id, //TODO - Validar merchant do JSON recebido no order
                        ]);
                        $ifoodOrders[] = $ifoodOrder;
                    }

                    //dd($ifoodEvents, $orderJson, $ifoodOrderMerchant, $ifoodOrders, $ifoodOrderMerchant ? $ifoodOrderMerchant->merchant_id : $ifoodEvent->merchant_id);

                    //Tira o evento da lista
                    $ifoodEvent->processed = 1;
                    $ifoodEvent->merchant_id = ($ifoodOrderMerchant ? $ifoodOrderMerchant->merchant_id : $ifoodEvent->merchant_id); //TODO - Update event com id_merchant
                    $ifoodEvent->processed_at = date("Y-m-d H:i:s");
                    $ifoodEvent->save();

                    //Confirma o broker, pelo merchant
                    $this->ifoodBroker = IfoodBroker::where("merchant_id", $ifoodEvent->merchant_id)->firstOrFail();


                }catch(\Exception $e){
                    throw $e;
                    //TODO - Tratar chave duplicada
                    
                }

                IntegratedOrders::dispatch($this->ifoodBroker, $ifoodEvent); //Aceita o pedido no ifood

    
            }elseif($ifoodEvent->code == "CAN"){
                //Tira o evento da lista
                $ifoodEvent->processed = 1;
                $ifoodEvent->processed_at = date("Y-m-d H:i:s");
                $ifoodEvent->save();

                $ifoodOrderMerchant = IfoodOrder::where("orderId", $ifoodEvent->orderId)->selectRaw("id, JSON_VALUE(JSON, '$.merchant.id') AS merchant_id")->first();
                $this->ifoodBroker = IfoodBroker::where("merchant_id", $ifoodOrderMerchant->merchant_id)->first();

                try{
                    //CANCELA PEDIDO
                    CanceledOrders::dispatch($this->ifoodBroker, $ifoodEvent);
                }catch(\Exception $e){
                    //TODO - Tratar chave duplicada
                    Log::info("IFOOD integration - Order cancelation", $e);
                }

       
            }
        }

        
        //return $ifoodOrders;
    }
   
}
