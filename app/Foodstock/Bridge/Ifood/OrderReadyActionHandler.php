<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodOrder;
use App\Models\IfoodEvent;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;

use App\Foodstock\Integration\Ifood\Order\OrderAction;
use App\Foodstock\Bridge\Ifood\BaseHandler;
use Illuminate\Support\Facades\Log;


class OrderReadyActionHandler extends BaseHandler{

    private IfoodEvent $ifoodEvent;

    public function __construct(IfoodBroker $ifoodBroker, IfoodEvent $ifoodEvent){
        parent::__construct($ifoodBroker);
        $this->ifoodEvent = $ifoodEvent;
        Log::info("IFOOD integration - Step OPTIONAL : ready to pickup order integration", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }
 
    public function handle(){
        $success = false;
        
        $orderAction = new OrderAction($this->ifoodBroker->accessToken, $this->ifoodEvent->orderId, EndPoints::OrderActionReadyToPickup);
        $success = $orderAction->request(); //Dá conhecimento

        $ifoodOrder = IfoodOrder::where("orderId", $this->ifoodEvent->orderId)->firstOrFail(); 
        if($success){
            $ifoodOrder->ready = 1;
            $ifoodOrder->save();
        }
        
        return $success;
    }
}
