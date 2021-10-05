<?php

namespace App\Foodstock\Bridge\Neemo;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Neemo\Order\OrderDetail;

use App\Models\Broker;
use App\Models\NeemoBroker;
use App\Models\NeemoEvent;
use App\Models\NeemoOrder;

use App\Foodstock\Bridge\Neemo\Events\ConcludedOrders;
use App\Foodstock\Bridge\Neemo\Events\CanceledOrders;
use App\Foodstock\Bridge\Neemo\Events\IntegratedOrders;
use App\Foodstock\Bridge\Neemo\BaseHandler;
use App\Foodstock\Integration\Neemo\Enums\OrderStatus;
use App\Foodstock\Integration\Neemo\RequestParameters\PoolingParameters;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdersHandler extends BaseHandler{

    public function __construct(NeemoBroker $neemoBroker){
        parent::__construct($neemoBroker);
        Log::info("NEEMO integration - Step TWO : get order detail", ["restaurant_id", $neemoBroker->restaurant_id]);
    }

    public function handle(){
        $neemoOrders = [];
        
        $neemoEvents = NeemoEvent::where("merchant_id", $this->neemoBroker->id)
            ->where("processed", 0)
            ->where("processing", 0)
            //->where("orderId", '70cfa2ba-465a-45fc-860c-bfe4eb92a45')
            //->where("code", "CAN")
            ->skip(0)
            ->take(50)
            //->whereRaw("date(createdAt) = '2021-09-06'")
            ->get();

        Log::info("NEEMO integration - Step TWO TOTAL", ["Merchant" => $this->neemoBroker->merchant_id, "Take" => count($neemoEvents)]);

        try{
            DB::beginTransaction();
            for($i = 0; $i< count($neemoEvents); $i++){
                $neemoEvents[$i]->processing = 1;
                $neemoEvents[$i]->save();
            } 
            DB::commit();

            $i = 0;
            try{
                foreach($neemoEvents as $neemoEvent){
                    $i++;
                    Log::info("NEEMO integration - Step TWO TAKE " . count($neemoEvents) . " PROCESSING " . $i);

                    if($neemoEvent->code == OrderStatus::Novo || $neemoEvent->code == OrderStatus::Confirmado){
                        $parameters = new PoolingParameters($this->neemoBroker->accessToken);
                        $orderDetail = new OrderDetail($parameters, $neemoEvent->orderId);
                        $orderJson = $orderDetail->request();

                        try{

                            //Cria e depois atualiza o merchant
                            $neemoOrder = NeemoOrder::updateOrCreate([
                                    'orderId' => $neemoEvent->orderId,
                                    'merchant_id' => $neemoEvent->merchant_id
                                ],
                                [
                                    'orderId' => $neemoEvent->orderId,
                                    'neemo_event_id' => $neemoEvent->id, 
                                    'merchant_id' => $neemoEvent->merchant_id,
                                    'json' => json_encode($orderJson) , 
                                    'processed' => 0
                                ]); 

                            $neemoOrders[] = $neemoOrder;

                            //Tira o evento da lista
                            $neemoEvent->processed = 1;
                            $neemoEvent->processed_at = date("Y-m-d H:i:s");
                            $neemoEvent->save();

                        }catch(\Exception $e){
                            //Em caso de erro, coloca para reprocessamento
                            $neemoEvent->processed = 0;
                            $neemoEvent->processed_at = null;
                            $neemoEvent->save();  
                            Log::error("NEEMO integration - Step TWO ERROR: Cant proccess order  " . $neemoEvent->orderId);
                        }

                        if($this->neemoBroker->enabled == 1){
                            IntegratedOrders::dispatch($this->neemoBroker, $neemoEvent); //Aceita o pedido no neemo
                        } 
                    }elseif($neemoEvent->code == OrderStatus::CanceladoRestaurante || $neemoEvent->code == OrderStatus::CanceladoAutomatico || $neemoEvent->code == OrderStatus::CanceladoRestauranteEstornado || $neemoEvent->code == OrderStatus::CanceladoAutomaticoEstornado){
    
                        $neemoOrderMerchant = NeemoOrder::where("orderId", $neemoEvent->orderId)->first();
                        if(is_object($neemoOrderMerchant)){
                            $neemoBroker = NeemoBroker::where("merchant_id", $neemoOrderMerchant->merchant_id)->first();
                            if(is_object($neemoBroker)){
                                try{
                                    //CANCELA PEDIDO
                                    if($neemoBroker->enabled == 1){
                                        //TODO - Tratar os cancelados
                                        //CanceledOrders::dispatch($neemoBroker, $neemoEvent);
                                    }
                                }catch(\Exception $e){
                                    $neemoEvent->processed = 0;
                                    $neemoEvent->processed_at = null;
                                    $neemoEvent->save();                        
                                    //TODO - Tratar chave duplicada
                                    Log::error("NEEMO integration - Order cancelation FAIL", ["order_id" => $neemoEvent->orderId]);
                                }
                            }
                        }
                    }elseif($neemoEvent->code == "CON" && $this->neemoBroker->conclude == 1){
                        //$this->concludedEventHandler($this->neemoBroker, $neemoEvent);
                    }
                }
            }catch(\Exception $e){
                $neemoEvent->processing = 0;
                $neemoEvent->save();
                Log::error("NEEMO integration - Error processing event", ["order_id" => $neemoEvent->orderId, "message" => $e->getMessage()]);
            }

        }catch(\Exception $e){
            DB::rollBack();
            Log::error("NEEMO integration - Error processing events", ["message" => $e->getMessage()]);

        }

    }

    private function concludedEventHandler($neemoBroker, $neemoEvent){
        try{
            ConcludedOrders::dispatch($neemoBroker, $neemoEvent);
        }catch(\Exception $e){
            $neemoEvent->processing = 0;
            $neemoEvent->concluded = 0;
            $neemoEvent->processed = 0;
            $neemoEvent->concluded_at = null;            
            Log::error("NEEMO integration - Order conclusion FAIL", ["order_id" => $neemoEvent->orderId, "message" => $e->getMessage()]);
        }        
    }
   
}
