<?php
namespace App\Foodstock\Bridge;

use App\Models\IfoodBroker;
use App\Foodstock\Bridge\Ifood\EventsHandler;
use App\Foodstock\Bridge\Ifood\Events\StartedProccess;
use Illuminate\Support\Facades\Log;

use Symfony\Component\Process\Process;

class OrderProcessorParallel{
    public function __construct(){

    }

    /*
        php artisan schedule:list
        php artisan schedule:work
    */

    public function start($input){
        $inicio = \time();
        $this->startIfood($input["brokerEndsWith"]);  
        echo sprintf("\n#####################################\nBroker com início em %s -> Tempo decorrido %s segundos \n\n", $input["brokerEndsWith"], \time() - $inicio);
    }

    private function startIfood($endsWith){
        Log::info("IFOOD integration - STARTED");
        $ifoodBrokers = IfoodBroker::where("broker_id", 1)
            ->where("enabled", 1)
            ->where("validated", 1)
            ->whereRaw("MOD(id, 10) = " . $endsWith)
            ->get();
        $inicio = \time();
        foreach($ifoodBrokers as $ifoodBroker){
            StartedProccess::dispatch($ifoodBroker); //Passo 1
            //break;
        }
    }
}
