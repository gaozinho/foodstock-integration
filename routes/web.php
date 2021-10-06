<?php

use Illuminate\Support\Facades\Route;

use App\Foodstock\Bridge\OrderProcessor;
use App\Foodstock\Bridge\OrderProcessorParallel;
use Illuminate\Http\Request;

use App\Foodstock\Actions\RestartOrderProccess;

Route::get('/order-processor', function () {
    (new OrderProcessor())->start();
});

Route::get('/order-processor/ifood', function () {
    (new OrderProcessor())->startIfood();
});

Route::get('/order-processor/neemo', function () {
    (new OrderProcessor())->startNeemo();
});


Route::get('/order-processor-parallel', function (Request $request) {
    $input = $request->all();
    //$data["start"] = time();
    (new OrderProcessorParallel())->start($input);
    //$data["end"] = time();
    //$data["time"] = $data["end"] - $data["start"];
    //echo json_encode($data);
});



/*
use App\Foodstock\Integration\Ifood\Authentication\UserCode;
use App\Foodstock\Integration\Ifood\Authentication\Token;
use App\Foodstock\Integration\Ifood\RequestParameters\BodyParameters;
use App\Foodstock\Integration\Ifood\RequestParameters\QueryStringParameters;
use App\Foodstock\Integration\Ifood\Order\Events\Pooling;
use App\Foodstock\Integration\Ifood\Order\Events\Acknowledgment;
use App\Foodstock\Integration\Ifood\Order\OrderDetail;
use App\Foodstock\Integration\Ifood\Order\OrderAction;
use App\Foodstock\Integration\Ifood\Enums\EndPoints;

use App\Foodstock\Integration\Backoffice\StartProduction;
use App\Foodstock\Integration\Backoffice\StartProductionBody;
use App\Foodstock\Bridge\Ifood\StartProductionHandler;

Route::get('/', function () {
    //phpinfo();
    //echo public_path();
    
    //$startProduction = new StartProduction(env("BACKOFFICE_TOKEN"), new StartProductionBody(1, 2, 'd18dd059-d9b2-4758-b97c-f8c506d80949', '{}'));
    //$response = $startProduction->request();
    //dd($response);

    //$authorization = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzUxMiJ9.eyJzdWIiOiI2MTIwMTAwYS1iNzJjLTQwYWUtYWUxMy0wYTFlOTYyNjRiZmUiLCJhcHBfbmFtZSI6ImZvb2RzdG9ja3Rlc3RlZCIsImF1ZCI6WyJjYXRhbG9nIiwiZmluYW5jaWFsIiwicmV2aWV3IiwibWVyY2hhbnQiLCJvcmRlciIsIm9hdXRoLXNlcnZlciJdLCJvd25lcl9uYW1lIjoid2FnbmVyZ29tZXNnb25jYWx2ZXMiLCJzY29wZSI6WyJjYXRhbG9nIiwicmV2aWV3IiwibWVyY2hhbnQiLCJvcmRlciIsImNvbmNpbGlhdG9yIl0sImlzcyI6ImlGb29kIiwidHlwZSI6ImNvbXBhY3QiLCJleHAiOjE2MjQzMTE3NTAsImlhdCI6MTYyNDI5MDE1MCwianRpIjoiMGUxMDNkNTMtNzdjOC00OGIzLTgxYTItZGZjNDU3MjBmZWZiIiwibWVyY2hhbnRfc2NvcGVkIjp0cnVlLCJjbGllbnRfaWQiOiI0ZjllMzhjNC02YTQ3LTRmZjEtYjVkZC03N2Y3NjQ4MDQzMDIifQ.nYZb1GdYMJx_7Yc5Fq52nimL3c-d8mdzzCGj6WZB9mKKzrYDCOzJ2Xb0cy4lCMr1KYLHL8p2SyqIz6bcQrp42cjT_nHOcJD2JMei5KVfwaEozqOy4hJwdnxGzWGLylbhaVwK4cWEMtxHeh0Sx6iHpEQdKgoWDUy5TaHdIVvyLvU";
    //$orderId = 'a0a43af9-3d55-45f9-b61b-45f40e2b9e82';

    //$querStringParameters = new QueryStringParameters(["types" => "PLC", "groups" => "ORDER_STATUS"]);
    //$pooling = new Pooling($authorization, $querStringParameters);
    //$json = $pooling->request();
    //($json); 

    //$acknowledgment = new Acknowledgment($authorization, json_encode($json));
    //$json = $acknowledgment->request();
    //dd($json);  

    //$orderDetail = new OrderDetail($authorization, $orderId);
    //$json = $orderDetail->request();
    //dd($json);


    //$orderAction = new OrderAction($authorization, $orderId, EndPoints::OrderActionDispatch);
    //$json = $orderAction->request();
    //dd($json);        

    //Refresh token
    $token = new Token(new BodyParameters(
        "refresh_token", 
        "4f9e38c4-6a47-4ff1-b5dd-77f764804302", 
        "nvoyf801irfcceo9w8p8cs2fyhitn0df806zh9spglp8qeefw3lcv9i2p8mj0drkuwxzj2t51dxpfzt62w0erpsnyyvnia4mbx3",
        "",
        "",
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzUxMiJ9.eyJzdWIiOiI2MTIwMTAwYS1iNzJjLTQwYWUtYWUxMy0wYTFlOTYyNjRiZmUiLCJpc3MiOiJpRm9vZCIsImV4cCI6MTYyNDY4MzY4NiwiaWF0IjoxNjI0MDc4ODg2LCJjbGllbnRfaWQiOiI0ZjllMzhjNC02YTQ3LTRmZjEtYjVkZC03N2Y3NjQ4MDQzMDIifQ.gs5XROh8GQKUK7xWIgL-2N9gGRpGGPDn66eMwa0UkiEfidfgDHt6b6xjG6BvtDkpm6km6GVq8cfyifNFQRnB2cuheMWs-NqzDHotsnpUjdSxxMrplwIwDt0cAnrFMA27kdjj1_1zpYTfsc11AvLU_9zDhcD2Fb5_4yDXuo2b4-M"
    ));
    $json = $token->request();
    dd($json);    

    //Distribuido - Só funciona uma vez
    $token = new Token(new BodyParameters(
        "authorization_code", 
        "4f9e38c4-6a47-4ff1-b5dd-77f764804302", 
        "nvoyf801irfcceo9w8p8cs2fyhitn0df806zh9spglp8qeefw3lcv9i2p8mj0drkuwxzj2t51dxpfzt62w0erpsnyyvnia4mbx3",
        "VVCL-TXNT",
        "2j4ngiwd59nqomptgslbsovi5xxfq42w60v4f9embxr0fynrejodczzrwdryt9typ8lhg3xscqbv467v1mjv8qninqwbijayxve"
    ));
    $json = $token->request();
    dd($json);

    //Centralizado
    $token = new Token(new BodyParameters(
        "client_credentials", 
        "d011632e-d785-40e0-8e21-34871d0392f2", 
        "14kxy8t76fxuyq751cd2cp9dfxo9hr08ayqgsbqwh5gycok9lrpg88w4346t2laxaahiemr6wm8951hk0ccowt559rzqjaxr8cks"
    ));
    $json = $token->request();
    dd($json);

    $userCode = new UserCode("4f9e38c4-6a47-4ff1-b5dd-77f764804302");
    $json = $userCode->request();
    dd($json);    

});

*/
