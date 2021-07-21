<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\IfoodBroker;
use App\Models\IfoodEvent;
use App\Models\IfoodOrder;
use Validator;
use App\Http\Resources\IfoodBroker as IfoodBrokerResource;
use App\Foodstock\Bridge\Ifood\OrderCancelationActionHandler;
use App\Foodstock\Integration\Ifood\RequestParameters\CancellationParameters;
   
class IfoodOrderCancellationController extends BaseController
{

    public function request(Request $request)
    {
        $ifoodBroker = null;
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'ifood_broker_id' => 'required',
            'ifood_order_id' => 'required',
            'reason' => 'required',
            'cancellationCode' => 'required|numeric',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        try{
            $ifoodBroker = IfoodBroker::findOrFail($input["ifood_broker_id"]);
    
            $orderCancelationActionHandler = new OrderCancelationActionHandler($ifoodBroker, $input["ifood_order_id"], new CancellationParameters($input["reason"], $input["cancellationCode"]));
            $success = $orderCancelationActionHandler->handle();

            $response = [
                'success' => $success,
                'message' => 'Cancellation status registered successfully.',
            ];

            return response()->json($response, 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Cant register cancellation status.',
            ], 500);
        }
        
    }
}