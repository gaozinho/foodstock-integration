<?php
namespace App\Foodstock\Integration;

use GuzzleHttp\Client;
use App\Foodstock\Integration\Neemo\Enums\EndPoints;
use App\Foodstock\Integration\Neemo\RequestParameters\PoolingParameters;

class BaseTokenIntegration{
    protected $httpClient;
    protected $token; 
    protected $formParameters;

    public function __construct(PoolingParameters $formParameters)
    {
        $this->formParameters = $formParameters;
        $this->httpClient = $this->getHttpClient(EndPoints::Base);
    }    

    protected function getHttpClient($baseEndpoint){
        return new Client(["base_uri" => $baseEndpoint, "verify" => false]);
    }    

    protected function parseHttpResponse($httpResponse){
        $json = json_decode($httpResponse->getBody()->getContents());
        return $json;
    } 
    
    protected function parseErrorResponse($httpResponse){
        /*
            200 - Success
            401 - Unauthorized - Falha de autenticação
            404 - Not Found - Ação inexistente ou que não retorne resultados
            500 - Internal Server Error
        */   
        $json = $this->parseHttpResponse($httpResponse);
        $json->statusCode = is_object($httpResponse) ? $httpResponse->getStatusCode() : 0;
        $json->reasonPhrase = is_object($httpResponse) ? $httpResponse->getReasonPhrase() : "Cant´t define an error reason.";
        $json->success = false;
        return $json;
    }     

    protected function setToken($token){
        $this->token = $token;
    }      
    
    protected function getToken(){
        return $this->token;
    }     
   
}
