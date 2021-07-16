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


class OrderConfirmActionHandler extends BaseHandler{

    private IfoodEvent $ifoodEvent;

    public function __construct(IfoodBroker $ifoodBroker, IfoodEvent $ifoodEvent){
        parent::__construct($ifoodBroker);
        $this->ifoodEvent = $ifoodEvent;
        Log::info("IFOOD integration - Step THREE : confim order integration", ["restaurant_id", $ifoodBroker->restaurant_id]);
    }
 
    public function handle(){
        $ifoodOrders = IfoodOrder::where("merchant_id", $this->ifoodBroker->merchant_id)
            ->where("processed", 0)->get();
        
        foreach($ifoodOrders as $ifoodOrder){
            if($this->ifoodBroker->acknowledgment == 1){
                $orderAction = new OrderAction($this->ifoodBroker->accessToken, $this->ifoodEvent->orderId, EndPoints::OrderActionConfirm);
                $success = $orderAction->request(); //Avisa que aceitou o pedido
            }
            $ifoodOrder->processed = 1;
            $ifoodOrder->save();                    
        }
    }
}
