<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Models\Broker;
use App\Models\IfoodBroker;

use App\Foodstock\Bridge\Ifood\RefreshTokenHandler;

class BaseHandler{

    protected IfoodBroker $ifoodBroker;

    public function __construct(IfoodBroker $ifoodBroker){
        $this->ifoodBroker = $ifoodBroker;
        $this->refreshToken();
    }

    private function refreshToken(){
        (new RefreshTokenHandler($this->ifoodBroker))->handle();
    }
}