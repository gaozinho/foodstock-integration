<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\IfoodBroker;
use Validator;
use App\Http\Resources\IfoodBroker as IfoodBrokerResource;
   
class IfoodBrokerController extends BaseController
{

    public function save(Request $request)
    {
        $ifoodBroker = null;
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'broker_id' => 'required',
            'merchant_id' => 'required',
            'restaurant_id' => 'required',
            'authorizationCode' => 'required',
            'authorizationCodeVerifier' => 'required',
            'accessToken' => 'required',
            'refreshToken' => 'required',
            'expiresIn' => 'required',
            'acknowledgment' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        try{
            $ifoodBroker = IfoodBroker::where("merchant_id", $input["merchant_id"])->firstOrFail();
            $ifoodBroker->broker_id = $input['broker_id'];
            $ifoodBroker->merchant_id = $input['merchant_id'];
            $ifoodBroker->restaurant_id = $input['restaurant_id'];
            $ifoodBroker->authorizationCode = $input['authorizationCode'];
            $ifoodBroker->authorizationCodeVerifier = $input['authorizationCodeVerifier'];
            $ifoodBroker->accessToken = $input['accessToken'];
            $ifoodBroker->refreshToken = $input['refreshToken'];
            $ifoodBroker->expiresIn = $input['expiresIn'];
            $ifoodBroker->acknowledgment = $input['acknowledgment'];
            $ifoodBroker->save();            
        }catch(\Exception $e){
            $ifoodBroker = IfoodBroker::create($input);
        }

        return $this->sendResponse(new IfoodBrokerResource($ifoodBroker), 'IfoodBroker saved successfully.');
    }

    public function delete(Request $request)
    {
        IfoodBroker::where("merchant_id", $request->merchant_id)->delete();
        return $this->sendResponse([], 'IfoodBroker deleted successfully.');
    }
}