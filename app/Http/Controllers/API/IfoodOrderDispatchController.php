<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\IfoodBroker;
use App\Models\IfoodEvent;
use App\Models\IfoodOrder;
use Validator;
use App\Http\Resources\IfoodBroker as IfoodBrokerResource;
use App\Foodstock\Bridge\Ifood\OrderDispatchActionHandler;
   
class IfoodOrderDispatchController extends BaseController
{

    public function dispatchOrder(Request $request)
    {
        $ifoodBroker = null;
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'ifood_broker_id' => 'required',
            'ifood_order_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $ifoodOrder = IfoodOrder::where("orderId", $input["ifood_order_id"])->firstOrFail();
        $ifoodOrder->dispatched = 1;
        $ifoodOrder->save();        
        
        $ifoodBroker = IfoodBroker::findOrFail($input["ifood_broker_id"]);
        $ifoodEvent = IfoodEvent::where("orderId", $input["ifood_order_id"])->firstOrFail();

        $orderDispatchActionHandler = new OrderDispatchActionHandler($ifoodBroker, $ifoodEvent);
        $success = $orderDispatchActionHandler->handle();

        
        $response = [
            'success' => $success,
            'message' => 'Dispatched successfully.',
        ];

        return response()->json($response, 200);
    }
}