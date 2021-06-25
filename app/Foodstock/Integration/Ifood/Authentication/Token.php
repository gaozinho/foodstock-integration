<?php
namespace App\Foodstock\Integration\Ifood\Authentication;

use App\Foodstock\Integration\Ifood\RequestParameters\BodyParameters;
use App\Foodstock\Integration\BaseIntegration;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;
use App\Foodstock\Integration\Ifood\Enums\GrantType;
use App\Foodstock\Integration\Interfaces\RequestInterface;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class Token extends BaseIntegration implements RequestInterface{

    protected BodyParameters $parameters;

    public function __construct(BodyParameters $parameters)
    {
        parent::__construct();
        $this->parameters = $parameters;
    }

    //Interface
    public function request(){
        try{
            $httpResponse = $this->httpClient->post(EndPoints::Token, ["form_params" => $this->parameters->toArray()]);
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