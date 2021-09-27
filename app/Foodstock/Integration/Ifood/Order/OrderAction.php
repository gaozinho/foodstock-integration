<?php
namespace App\Foodstock\Integration\Ifood\Order;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;
use App\Foodstock\Integration\Ifood\Enums\GrantType;
use App\Foodstock\Integration\Interfaces\RequestInterface;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class OrderAction extends BaseIntegration implements RequestInterface{

    private $uuid;
    private $action;

    public function __construct($authorization, $uuid, $action)
    {
        parent::__construct();
        $this->uuid = $uuid;
        $this->action = $action;
        $this->setAuthorizationHeader($authorization);
    }

    //Interface
    public function request(){
        try{
            $httpResponse = $this->httpClient->post($this->mountOrderActionUri(), $this->headers);
            $json = $this->parseHttpResponse($httpResponse);
            return true;
        }catch(BadResponseException $exception){ //400, 500 Family
            return $this->parseErrorResponse("{}");
        }catch(ConnectException $connException){
            return $this->parseErrorResponse($connException->getResponse());
        }catch(\Exception $genericException){
            
        }
    }

    private function mountOrderActionUri(){
        return sprintf($this->action, $this->uuid);
    }


}