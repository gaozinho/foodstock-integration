<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodOrder;
use App\Models\IfoodEvent;

use App\Foodstock\Integration\Backoffice\ConcludeProduction;
use App\Foodstock\Integration\Ifood\Order\Events\Acknowledgment;
use App\Foodstock\Bridge\Ifood\BaseHandler;
use App\Foodstock\Bridge\Ifood\Events\StartedOrderProduction;

use Illuminate\Support\Facades\Log;

use App\Foodstock\Bridge\Ifood\AcknowledgmentsHandler;

class ConcludedProductionHandler extends BaseHandler{

    private IfoodEvent $ifoodEvent;

    public function __construct(IfoodBroker $ifoodBroker, IfoodEvent $ifoodEvent){
        parent::__construct($ifoodBroker);
        $this->ifoodEvent = $ifoodEvent;
        Log::info("IFOOD integration - Step CONCLUDED : Backoffice : send conclusion request o backoffice system", ["restaurant_id" => $ifoodBroker->restaurant_id, "order" => $ifoodEvent->orderId]);
    }
 
    public function handle(){
        $concludedProduction = new ConcludeProduction(env("BACKOFFICE_TOKEN"), $this->ifoodEvent->orderId);
        $response = $concludedProduction->request(); //Envia fato para o backoffice

        $jsonObjects[] = json_decode($this->ifoodEvent->json);
        $acknowledgment = new Acknowledgment($this->ifoodBroker->accessToken, json_encode($jsonObjects));   
        $ack = $acknowledgment->request(); //TODO - Tratar token expirado             
        
        if($ack){
            $this->ifoodEvent->concluded = 1;
            $this->ifoodEvent->processed = 1;
            $this->ifoodEvent->concluded_at = date("Y-m-d H:i:s");
            $this->ifoodEvent->save();     
        }

    }
}
