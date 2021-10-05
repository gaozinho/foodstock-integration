<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\NeemoBroker;
use App\Models\NeemoEvent;
use App\Models\NeemoOrder;
use Validator;
use App\Http\Resources\NeemoBroker as NeemoBrokerResource;
use App\Foodstock\Bridge\Neemo\OrderDispatchActionHandler;
   
class NeemoOrderDispatchController extends BaseController
{

    public function dispatchOrder(Request $request)
    {
        $neemoBroker = null;
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'neemo_broker_id' => 'required',
            'neemo_order_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        try{
            //$neemoOrder = NeemoOrder::where("orderId", $input["neemo_order_id"])->firstOrFail();
            //$neemoOrder->dispatched = 1;
            //$neemoOrder->save();        
            
            $neemoBroker = NeemoBroker::findOrFail($input["neemo_broker_id"]);
            $neemoEvent = NeemoEvent::where("orderId", $input["neemo_order_id"])->firstOrFail();

            $orderDispatchActionHandler = new OrderDispatchActionHandler($neemoBroker, $neemoEvent);
            $success = $orderDispatchActionHandler->handle();

            
            $response = [
                'success' => $success,
                'message' => 'Dispatched successfully.',
            ];

            return response()->json($response, 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Cant dispatch order.',
            ], 500);
        }
    }
}