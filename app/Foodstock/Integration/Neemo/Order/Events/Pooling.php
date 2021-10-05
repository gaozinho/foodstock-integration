<?php
namespace App\Foodstock\Integration\Neemo\Order\Events;

use App\Foodstock\Integration\BaseTokenIntegration;
use App\Foodstock\Integration\Neemo\Enums\EndPoints;
use App\Foodstock\Integration\Neemo\Enums\GrantType;
use App\Foodstock\Integration\Interfaces\RequestInterface;
use App\Foodstock\Integration\Neemo\RequestParameters\PoolingParameters;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class Pooling extends BaseTokenIntegration implements RequestInterface{

    public function __construct(PoolingParameters $formParameters)
    {
        parent::__construct($formParameters);
    }

    //Interface
    public function request(){

        try{
            $httpResponse = $this->httpClient->post(EndPoints::EventsPooling, $this->formParameters->formParameters());
            $json = $this->parseHttpResponse($httpResponse);
            if(isset($json->code) && $json->code > 299){
                throw new \Exception($json->name);
            }

            return $json;
        }catch(BadResponseException $exception){ //400, 500 Familys
            return $this->parseErrorResponse($exception->getResponse());
        }catch(ConnectException $connException){
            $json = new \stdClass();
            $json->statusCode = 0;
            $json->reasonPhrase = "Can´t connect to neemo server.";
            $json->success = false;
            return $json;
        }catch(\Exception $genericException){
            $json = new \stdClass();
            $json->statusCode = 0;
            $json->reasonPhrase = "Undefined exception";
            $json->success = false;
            return $json;
        }
    }
}