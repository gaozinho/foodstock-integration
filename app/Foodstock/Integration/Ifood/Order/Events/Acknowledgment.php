<?php
namespace App\Foodstock\Integration\Ifood\Order\Events;

use App\Foodstock\Integration\Ifood\Authentication\TokenParameters;
use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;
use App\Foodstock\Integration\Ifood\Enums\GrantType;
use App\Foodstock\Integration\Interfaces\RequestInterface;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class Acknowledgment extends BaseIntegration implements RequestInterface{

    
    public function __construct($authorization, $requestBody)
    {
        parent::__construct();
        $this->setAuthorizationHeader($authorization);
        $this->requestBody = $requestBody;
    }

    //Interface
    public function request(){
        try{
            $httpResponse = $this->httpClient->post(EndPoints::EventsAcknowledgment, $this->formatRequestParameters());
            $this->parseHttpResponse($httpResponse);
            return true; //Accepted. The request may be processed asynchronously
        }catch(BadResponseException $exception){ //400, 500 Family
            return $this->parseErrorResponse($exception->getResponse());
        }catch(ConnectException $connException){
            return $this->parseErrorResponse($connException->getResponse());
        }catch(\Exception $genericException){
            
        }
    }

    private function formatRequestParameters(){
        $this->formatRequestHeader();
        return array_merge(["body" => $this->getRequestBody()], $this->getHeaders());
    }

    private function formatRequestHeader(){
        $this->headers["headers"]["Accept"] = "*/*";
        $this->headers["headers"]["Content-Type"] = "application/json";
    }

}