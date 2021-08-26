<?php
namespace App\Foodstock\Integration\Ifood\Order\Events;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;
use App\Foodstock\Integration\Ifood\Enums\GrantType;
use App\Foodstock\Integration\Interfaces\RequestInterface;
use App\Foodstock\Integration\Ifood\RequestParameters\QueryStringParameters;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class Pooling extends BaseIntegration implements RequestInterface{

    public function __construct($authorization, QueryStringParameters $queryStringParameters)
    {
        parent::__construct();

        $this->setAuthorizationHeader($authorization);

        $this->queryStringParameters = $queryStringParameters;
        //https://developer.ifood.com.br/docs/guides/order/events#order_status
        //https://developer.ifood.com.br/docs/guides/order/events#grupos-de-eventos
    }

    //Interface
    public function request(){
        try{
            $httpResponse = $this->httpClient->get(EndPoints::EventsPooling . $this->queryStringParameters->toQueryString(), $this->headers);
            $json = $this->parseHttpResponse($httpResponse);
            return $json;
        }catch(BadResponseException $exception){ //400, 500 Family
            return $this->parseErrorResponse($exception->getResponse());
        }catch(ConnectException $connException){
            $json = new \stdClass;
            $json->statusCode = 0;
            $json->reasonPhrase = "Can´t connect to ifood server.";
            $json->success = false;
            return $json;
        }catch(\Exception $genericException){
            $json = new stdClass;
            $json->statusCode = 0;
            $json->reasonPhrase = "Undefined exception";
            $json->success = false;
            return $json;
        }
    }
}