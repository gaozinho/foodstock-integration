<?php
namespace App\Foodstock\Integration\Neemo\Order;

use App\Foodstock\Integration\BaseTokenIntegration;
use App\Foodstock\Integration\Neemo\Enums\EndPoints;
use App\Foodstock\Integration\Neemo\Enums\GrantType;
use App\Foodstock\Integration\Interfaces\RequestInterface;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class OrderAction extends BaseTokenIntegration implements RequestInterface{

    private $uuid;
    private $action;

    public function __construct($formParameters, $uuid)
    {
        parent::__construct($formParameters);
        $this->uuid = $uuid;
    }

    //Interface
    public function request(){
        try{
            $httpResponse = $this->httpClient->put(sprintf(EndPoints::EventsAcknowledgment, $this->uuid), $this->formParameters->formParameters());
            $json = $this->parseHttpResponse($httpResponse);
            if($json->code == 200) return true;
            else throw new \BadResponseException("Can't confirm order " . $this->uuid);
        }catch(BadResponseException $exception){ //400, 500 Family
            return $this->parseErrorResponse("{}");
        }catch(ConnectException $connException){
            return $this->parseErrorResponse($connException->getResponse());
        }catch(\Exception $genericException){

        }
    }

    private function mountOrderActionUri(){
        return sprintf($this->action, $this->uuid);
    }


}