<?php
namespace App\Foodstock\Integration\Backoffice;

use App\Foodstock\Integration\BaseIntegration; //TODO - Revisar pacotes

use App\Foodstock\Integration\Interfaces\RequestInterface; //TODO - Revisar pacotes

use App\Foodstock\Integration\Backoffice\CancelProductionBody;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class CancelProduction extends BaseIntegration implements RequestInterface{

    public function __construct($authorization, CancelProductionBody $requestBody)
    {
        parent::__construct();
        $this->setAuthorizationHeader($authorization);
        $this->requestBody = $requestBody;
    }

    //Interface
    public function request(){
        try{
            $url = env("BACKOFFICE_CANCEL_PRODUCTION_URI");
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
        return array_merge(["form_params" => $this->getRequestBody()->toArray()], $this->headers);
    }


}