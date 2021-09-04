<?php
namespace App\Foodstock\Integration\Backoffice;

use App\Foodstock\Integration\BaseIntegration; //TODO - Revisar pacotes

use App\Foodstock\Integration\Interfaces\RequestInterface; //TODO - Revisar pacotes

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class ConcludeProduction extends BaseIntegration implements RequestInterface{

    private $orderId;

    public function __construct($authorization, $orderId)
    {
        parent::__construct();
        $this->setAuthorizationHeader($authorization);
        $this->orderId = $orderId;
    }

    //Interface
    public function request(){
        try{
            $url = env("BACKOFFICE_CONCLUDE_PRODUCTION_URI");
            $httpResponse = $this->httpClient->post($url, $this->formatRequestParameters());
            $json = $this->parseHttpResponse($httpResponse);
            
            return $json;
        }catch(BadResponseException $exception){ //400, 500 Family
            return $this->parseErrorResponse($exception->getResponse());
        }catch(ConnectException $connException){
           // return $this->parseErrorResponse($connException->getResponse());
        }
    }

    private function formatRequestParameters(){
        return array_merge(["form_params" => ["order_id" => $this->orderId]], $this->headers);
    }


}