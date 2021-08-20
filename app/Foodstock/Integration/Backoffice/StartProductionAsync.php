<?php
namespace App\Foodstock\Integration\Backoffice;

use App\Foodstock\Integration\BaseIntegration; //TODO - Revisar pacotes

use App\Foodstock\Integration\Interfaces\RequestInterface; //TODO - Revisar pacotes

use App\Foodstock\Integration\Backoffice\StartProductionBody;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

use Illuminate\Support\Facades\Log;

class StartProductionAsync extends BaseIntegration implements RequestInterface{

    private $startProductionBodies;

    public function __construct($authorization, $startProductionBodies)
    {
        parent::__construct();
        $this->setAuthorizationHeader($authorization);
        $this->startProductionBodies = $startProductionBodies;
    }

    //Interface
    public function request(){

            $uri = env("BACKOFFICE_START_PRODUCTION_URI");
            $headers = $this->getHeaders();

            $client = $this->httpClient;

            $requests = function ($startProductionBodies) use ($uri, $headers) {
                for ($i = 0; $i < count($startProductionBodies); $i++) {
                    $headers["form_params"] = $startProductionBodies[$i]->toArray();
                    yield new Request('POST', $uri, $headers);
                }
            };

            $startProductionBodies = $this->startProductionBodies;

            $pool = new Pool($client, $requests($this->startProductionBodies), [
                'concurrency' => 5,
                'fulfilled' => function (Response $response, $index) use ($startProductionBodies) {
                    //Log::info("####################");

                    $startProductionBodies[$index]->order->started_production = 1;
                    $startProductionBodies[$index]->order->save();

                },
                'rejected' => function (RequestException $reason, $index) {
                    //Log::info("FALHA####################");
                    // this is delivered each failed request
                },
            ]);       

            $promise = $pool->promise();  

            $promise->wait();   

    } 
}