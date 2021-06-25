<?php
namespace App\Foodstock\Integration\Ifood\RequestParameters;

use App\Foodstock\Integration\Ifood\Enums\GrantType;

class QueryStringParameters{

    private $parameters;

    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    public function toQueryString(){
        if(count($this->parameters) == 0) return "";
        $reformatedArray = [];
        foreach($this->parameters as $key => $value){
            $reformatedArray[] = $key . "=" . $value;
        }
        return "?" . implode("&", $reformatedArray);
    }
}