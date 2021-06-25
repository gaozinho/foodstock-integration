<?php
namespace App\Foodstock\Integration\Ifood\Order;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;
use App\Foodstock\Integration\Ifood\Enums\GrantType;
use App\Foodstock\Integration\Interfaces\RequestInterface;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class OrderDetail extends BaseIntegration implements RequestInterface{

    private $uuid;

    public function __construct($authorization, $uuid)
    {
        parent::__construct();
        $this->uuid = $uuid;
        $this->setAuthorizationHeader($authorization);
    }

    //Interface
    public function request(){
        try{
            $httpResponse = $this->httpClient->get(EndPoints::OrderDetail . $this->uuid, $this->headers);
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