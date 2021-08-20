<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Foodstock\Integration\BaseIntegration;

use App\Models\Broker;
use App\Models\IfoodBroker;
use App\Models\IfoodOrder;
use App\Models\IfoodEvent;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;

use App\Foodstock\Integration\Ifood\Order\OrderCancellation;
use App\Foodstock\Bridge\Ifood\BaseHandler;
use Illuminate\Support\Facades\Log;
use App\Foodstock\Integration\Ifood\RequestParameters\CancellationParameters;


class OrderCancelationActionHandler extends BaseHandler{

    private $ifood_order_id;
    private $cancellationParameters;

    public function __construct(IfoodBroker $ifoodBroker, $ifood_order_id, CancellationParameters $cancellationParameters){
        parent::__construct($ifoodBroker);
        $this->ifood_order_id = $ifood_order_id;
        $this->cancellationParameters = $cancellationParameters;
        Log::info("IFOOD integration - Step OPTIONAL : cancelation order integration", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }
 
    public function handle(){
        $success = false;
        
        $orderAction = new OrderCancellation($this->ifoodBroker->accessToken, $this->ifood_order_id, $this->cancellationParameters, EndPoints::OrderActionRequestCancellation);
        $success = $orderAction->request(); //Dá conhecimento

        $ifoodOrder = IfoodOrder::where("orderId", $this->ifood_order_id)->firstOrFail(); 
        if($success){
            $ifoodOrder->canceled_production = 1;
            $ifoodOrder->save();
        }
        
        //return $success;
    }
}
