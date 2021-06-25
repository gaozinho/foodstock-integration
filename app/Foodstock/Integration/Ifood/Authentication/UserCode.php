<?php
namespace App\Foodstock\Integration\Ifood\Authentication;

use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;
use App\Foodstock\Integration\Interfaces\RequestInterface;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class UserCode extends BaseIntegration implements RequestInterface{

    protected $credentials = [
        "clientId" => "",
    ];

    public function __construct($clientId)
    {
        parent::__construct();
        $this->credentials["clientId"] = $clientId;
    }

    //Interface
    public function request(){
        try{
            $httpResponse = $this->httpClient->post(EndPoints::UserCode, ["form_params" => $this->credentials]);
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