<?php
namespace App\Foodstock\Bridge\Neemo;

use App\Models\Broker;
use App\Models\NeemoBroker;

use App\Foodstock\Bridge\Neemo\RefreshTokenHandler;

class BaseHandler{

    protected NeemoBroker $neemoBroker;

    public function __construct(NeemoBroker $neemoBroker){
        $this->neemoBroker = $neemoBroker;
        //$this->refreshToken();
    }

    private function refreshToken(){
        //(new RefreshTokenHandler($this->neemoBroker))->handle();
    }
}