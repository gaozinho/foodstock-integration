<?php
namespace App\Foodstock\Integration;

use GuzzleHttp\Client;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;
use App\Foodstock\Integration\Ifood\RequestParameters\QueryStringParameters;
use App\Foodstock\Integration\Ifood\RequestParameters\BodyParameters;

class BaseIntegration{
    protected $httpClient;
    protected BodyParameters $bodyParameters;
    protected $requestBody;
    protected QueryStringParameters $queryStringParameters;
    protected $headers = [
        'headers' => [
            'Authorization' => '',
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ],
    ];    

    public function __construct()
    {
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
            400	- Bad Request. One or more request parameters were not valid.
            401	- Expired token
            403	- Forbidden
            404	- Not Found
            413	- The request would query too many merchants (maximum is 100).
            415	- Unsupported Media Type
            500	- Internal Server Error
        */   
        $json = $this->parseHttpResponse($httpResponse);
        $json->statusCode = $httpResponse->getStatusCode();
        $json->reasonPhrase = $httpResponse->getReasonPhrase();
        $json->success = false;
        return $json;
    }     

    protected function setAuthorizationHeader($authorization){
        $this->headers['headers']['Authorization'] = "Bearer " . $authorization;
    }

    protected function setBodyParameters(BodyParameters $requestBody){
        $this->bodyParameters = $bodyParameters;
    } 
    
    protected function setRequestBody($requestBody){
        $this->requestBody = $requestBody;
    }     
    
    protected function setQueryStringParameters(QueryStringParameters $queryStringParameters){
        $this->queryStringParameters = $queryStringParameters;
    }   

    protected function getHeaders(){
        return $this->headers;
    }

    protected function getRequestBody(){
        return $this->requestBody;
    } 
    
    protected function getBodyParameters(){
        return $this->requestBody;
    }     
    
    protected function getQueryStringParameters(){
        return $this->queryStringParameters;
    }       
}
