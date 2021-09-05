<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodEvent;

use App\Foodstock\Integration\Ifood\Order\Events\Acknowledgment;
use App\Foodstock\Bridge\Ifood\BaseHandler;
use Illuminate\Support\Facades\Log;

class CancelAcknowledgmentsHandler extends BaseHandler{


    public function __construct(IfoodBroker $ifoodBroker){
        parent::__construct($ifoodBroker);
        Log::info("IFOOD integration - Step CANCEL : acknowledgment", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }

    public function handle(){
        $jsonObjects = [];
        $ifoodEvents = IfoodEvent::where("merchant_id", $this->ifoodBroker->merchant_id)
            //->where("processed", 1)
            ->where("code", "CAN")
            ->where("acknowledgment", 0)
            ->get();

        foreach($ifoodEvents as $ifoodEvent){
            $jsonObjects[] = json_decode($ifoodEvent->json);          
        }            

        if(count($jsonObjects) > 0){
            $acknowledgment = new Acknowledgment($this->ifoodBroker->accessToken, json_encode($jsonObjects));   
            $ack = $acknowledgment->request(); //TODO - Tratar token expirado

            if($ack){
                foreach($ifoodEvents as $ifoodEvent){
                    $ifoodEvent->acknowledgment = 1;
                    $ifoodEvent->acknowledgment_at = date("Y-m-d H:i:s");
                    $ifoodEvent->save();            
                }
            }
        }else{
            //return true;
        }
    }    
   
}
