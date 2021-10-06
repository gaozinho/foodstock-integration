<?php
namespace App\Foodstock\Actions;

use Illuminate\Support\Facades\DB;

use App\Models\IfoodOrder;
use App\Models\IfoodEvent;

use App\Models\NeemoOrder;
use App\Models\NeemoEvent;

use Illuminate\Support\Facades\Log;

class RestartOrderProccess{

    public static function restart($order_id, $broker_id){

        try{
            DB::beginTransaction();
            if($broker_id == 1){
                IfoodEvent::where("orderId", $order_id)->update([
                    "processed" => 0,
                    "processed_at" => null,
                    "acknowledgment" => 0,
                    "acknowledgment_at" => null,
                    "processing" => 0,
                    "concluded" => 0,
                    "concluded_at" => null,
                    "tries" => 2,
                    
                ]);
                IfoodOrder::where("orderId", $order_id)->update([
                    "processed" => 0,
                    "started_production" => 0,
                    "dispatched" => 0,
                    "canceled_production" => 0,
                    "ready" => 0,
                    "tries" => 2,
                ]);            
            }else if($broker_id == 3){
                NeemoEvent::where("orderId", $order_id)->update([
                    "processed" => 0,
                    "processed_at" => null,
                    "acknowledgment" => 0,
                    "acknowledgment_at" => null,
                    "processing" => 0,
                    "concluded" => 0,
                    "concluded_at" => null,
                    "tries" => 2,
                ]);
                NeemoOrder::where("orderId", $order_id)->update([
                    "processed" => 0,
                    "started_production" => 0,
                    "dispatched" => 0,
                    "canceled_production" => 0,
                    "ready" => 0,
                    "tries" => 2,
                ]);            
            }            
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
}