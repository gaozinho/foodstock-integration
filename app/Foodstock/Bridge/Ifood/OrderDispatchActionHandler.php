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


class OrderDispatchActionHandler extends BaseHandler{

    private IfoodEvent $ifoodEvent;

    public function __construct(IfoodBroker $ifoodBroker, IfoodEvent $ifoodEvent){
        parent::__construct($ifoodBroker);
        $this->ifoodEvent = $ifoodEvent;
        Log::info("IFOOD integration - Step SIX : dispatch order integration", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }
 
    public function handle(){

        $success = false;

        //if($this->ifoodBroker->acknowledgment == 1){

                $orderAction = new OrderAction($this->ifoodBroker->accessToken, $this->ifoodEvent->orderId, EndPoints::OrderActionDispatch);
                $success = $orderAction->request(); //Dá conhecimento

                $ifoodOrder = IfoodOrder::where("orderId", $this->ifoodEvent->orderId)->firstOrFail(); 
                if($success){
                    $ifoodOrder->dispatched = 1;
                    $ifoodOrder->save();
                }

        //}else{
            //$success = true; //Finaliza sem informar dispatch
        //}     
        
        //return $success;
    }
}
