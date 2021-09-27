<?php

namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Order\OrderDetail;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodEvent;
use App\Models\IfoodOrder;

use App\Foodstock\Bridge\Ifood\Events\ConcludedOrders;
use App\Foodstock\Bridge\Ifood\Events\CanceledOrders;
use App\Foodstock\Bridge\Ifood\Events\IntegratedOrders;
use App\Foodstock\Bridge\Ifood\BaseHandler;

use Illuminate\Support\Facades\DB;
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
            //->where("orderId", '70cfa2ba-465a-45fc-860c-bfe4eb92a45')
            //->where("code", "CAN")
            ->skip(0)
            ->take(50)
            //->whereRaw("date(createdAt) = '2021-09-06'")
            ->get();

        Log::info("IFOOD integration - Step TWO TOTAL", ["Merchant" => $this->ifoodBroker->merchant_id, "Take" => count($ifoodEvents)]);

        try{
            DB::beginTransaction();
            for($i = 0; $i< count($ifoodEvents); $i++){
                $ifoodEvents[$i]->processing = 1;
                $ifoodEvents[$i]->save();
            } 
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
        }

        $i = 0;
        try{
            foreach($ifoodEvents as $ifoodEvent){
                $i++;
                Log::info("IFOOD integration - Step TWO TAKE " . count($ifoodEvents) . " PROCESSING " . $i);

                if($ifoodEvent->code == "PLC"){
                    $orderDetail = new OrderDetail($this->ifoodBroker->accessToken, $ifoodEvent->orderId);
                    $orderJson = $orderDetail->request();
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

                        //Verifica se a integração da loja está ativada
                        //Tira o evento da lista
                        $ifoodEvent->processed = 1;
                        $ifoodEvent->merchant_id = ($ifoodOrderMerchant ? $ifoodOrderMerchant->merchant_id : $ifoodEvent->merchant_id); //TODO - Update event com id_merchant
                        $ifoodEvent->processed_at = date("Y-m-d H:i:s");
                        $ifoodEvent->save();

                        //Confirma o broker, pelo merchant
                        $this->ifoodBroker = IfoodBroker::where("merchant_id", $ifoodEvent->merchant_id)->firstOrFail();
                    }catch(\Exception $e){
                        //Em caso de erro, coloca para reprocessamento
                        $ifoodEvent->processed = 0;
                        $ifoodEvent->processed_at = null;
                        $ifoodEvent->save();  
                        Log::error("IFOOD integration - Step TWO ERROR: Cant proccess order  " . $ifoodEvent->orderId);
                    }

                    if($this->ifoodBroker->enabled == 1){
                        IntegratedOrders::dispatch($this->ifoodBroker, $ifoodEvent); //Aceita o pedido no ifood
                    } 
                }elseif($ifoodEvent->code == "CAN"){

                    $ifoodOrderMerchant = IfoodOrder::where("orderId", $ifoodEvent->orderId)->selectRaw("id, JSON_VALUE(JSON, '$.merchant.id') AS merchant_id")->first();
                    if(is_object($ifoodOrderMerchant)){
                        $ifoodBroker = IfoodBroker::where("merchant_id", $ifoodOrderMerchant->merchant_id)->first();
                        if(is_object($ifoodBroker)){
                            try{
                                //CANCELA PEDIDO
                                if($ifoodBroker->enabled == 1){
                                    CanceledOrders::dispatch($ifoodBroker, $ifoodEvent);
                                }
                            }catch(\Exception $e){
                                $ifoodEvent->processed = 0;
                                $ifoodEvent->processed_at = null;
                                $ifoodEvent->save();                        
                                //TODO - Tratar chave duplicada
                                Log::error("IFOOD integration - Order cancelation FAIL", ["order_id" => $ifoodEvent->orderId]);
                            }
                        }
                    }
                }elseif($ifoodEvent->code == "CON" && $this->ifoodBroker->conclude == 1){
                    $this->concludedEventHandler($this->ifoodBroker, $ifoodEvent);
                }
            }
        }catch(\Exception $e){
            $ifoodEvent->processing = 1;
            $ifoodEvent->save();
            Log::error("IFOOD integration - Error processing event", ["order_id" => $ifoodEvent->orderId, "message" => $e->getMessage()]);
        }
    }

    private function concludedEventHandler($ifoodBroker, $ifoodEvent){
        try{
            ConcludedOrders::dispatch($ifoodBroker, $ifoodEvent);
        }catch(\Exception $e){
            $ifoodEvent->processing = 0;
            $ifoodEvent->concluded = 0;
            $ifoodEvent->processed = 0;
            $ifoodEvent->concluded_at = null;            
            Log::error("IFOOD integration - Order conclusion FAIL", ["order_id" => $ifoodEvent->orderId, "message" => $e->getMessage()]);
        }        
    }
   
}
