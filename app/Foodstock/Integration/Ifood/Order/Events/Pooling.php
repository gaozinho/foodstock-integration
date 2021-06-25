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
            return $this->parseErrorResponse($connException->getResponse());
        }catch(\Exception $genericException){
            
        }
    }
}