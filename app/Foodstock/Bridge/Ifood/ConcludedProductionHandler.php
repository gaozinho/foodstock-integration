<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodOrder;
use App\Models\IfoodEvent;

use App\Foodstock\Integration\Backoffice\ConcludeProduction;

use App\Foodstock\Bridge\Ifood\BaseHandler;
use App\Foodstock\Bridge\Ifood\Events\StartedOrderProduction;

use Illuminate\Support\Facades\Log;

class ConcludedProductionHandler extends BaseHandler{

    private IfoodEvent $ifoodEvent;

    public function __construct(IfoodBroker $ifoodBroker, IfoodEvent $ifoodEvent){
        parent::__construct($ifoodBroker);
        $this->ifoodEvent = $ifoodEvent;
        Log::info("IFOOD integration - Step CONCLUDED : Backoffice : send conclusion request o backoffice system", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }
 
    public function handle(){
        $concludedProduction = new ConcludeProduction(env("BACKOFFICE_TOKEN"), $this->ifoodEvent->orderId);
        $response = $concludedProduction->request(); //Envia fato para o backoffice
    }
}
