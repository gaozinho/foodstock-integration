<?php
namespace App\Foodstock\Integration\Neemo\Order;

use App\Foodstock\Integration\BaseTokenIntegration;
use App\Foodstock\Integration\Neemo\Enums\EndPoints;
use App\Foodstock\Integration\Neemo\Enums\GrantType;
use App\Foodstock\Integration\Interfaces\RequestInterface;
use App\Foodstock\Integration\Neemo\RequestParameters\PoolingParameters;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class OrderDetail extends BaseTokenIntegration implements RequestInterface{

    private $uuid;

    public function __construct(PoolingParameters $formParameters, $uuid)
    {
        parent::__construct($formParameters);
        $this->uuid = $uuid;
    }

    //Interface
    public function request(){
        try{
            $httpResponse = $this->httpClient->post(sprintf(EndPoints::OrderDetail, $this->uuid), $this->formParameters->formParameters());
            $json = $this->parseHttpResponse($httpResponse);
            if($json->code < 299) return $json;
        }catch(BadResponseException $exception){ //400, 500 Family
            return $this->parseErrorResponse($exception->getResponse());
        }catch(ConnectException $connException){
            return $this->parseErrorResponse($connException->getResponse());
        }catch(\Exception $genericException){
            
        }        

    }


}