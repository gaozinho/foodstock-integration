<?php
namespace App\Foodstock\Integration\Ifood\Order;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;
use App\Foodstock\Integration\Ifood\Enums\GrantType;
use App\Foodstock\Integration\Interfaces\RequestInterface;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use App\Foodstock\Integration\Ifood\RequestParameters\CancellationParameters;

class OrderCancellation extends BaseIntegration implements RequestInterface{

    private $uuid;
    private $action;
    private $cancellationParameters;

    public function __construct($authorization, $uuid, CancellationParameters $cancellationParameters, $action)
    {
        parent::__construct();
        $this->uuid = $uuid;
        $this->action = $action;
        $this->cancellationParameters = $cancellationParameters;
        $this->setAuthorizationHeader($authorization);
    }

    //Interface
    public function request(){
        try{
            //dd($this->mountOrderActionUri(), $this->formatRequestParameters());
            $httpResponse = $this->httpClient->post($this->mountOrderActionUri(), $this->formatRequestParameters());
            //$json = $this->parseHttpResponse($httpResponse);
            return true;
        }catch(BadResponseException $exception){ //400, 500 Family
            //dd($exception);
            return $this->parseErrorResponse($exception->getResponse());
        }catch(ConnectException $connException){
            //dd($connException);
            return $this->parseErrorResponse($connException->getResponse());
        }
    }

    private function formatRequestParameters(){
        //dd($this->cancellationParameters->toJson());
        $this->formatRequestHeader();
        return array_merge(["body" => $this->cancellationParameters->toJson()], $this->getHeaders());
    }    

    private function mountOrderActionUri(){
        return sprintf($this->action, $this->uuid);
    }

    private function formatRequestHeader(){
        $this->headers["headers"]["Accept"] = "*/*";
        $this->headers["headers"]["Content-Type"] = "application/json";
    }    
}