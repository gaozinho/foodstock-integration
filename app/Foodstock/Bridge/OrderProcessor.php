<?php
namespace App\Foodstock\Bridge;

use App\Models\IfoodBroker;
use App\Foodstock\Bridge\Ifood\EventsHandler;
use App\Foodstock\Bridge\Ifood\Events\StartedProccess;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class OrderProcessor{
    public function __construct(){
        
    }

    public function start(){
        $this->startIfood();
    }

    private function startIfood(){
        Log::info("IFOOD integration - STARTED");

        $ifoodBrokers = IfoodBroker::where("broker_id", 1)
            ->where("enabled", 1)
            ->where("validated", 1)
            ->get();
        $inicio = \time();
        foreach($ifoodBrokers as $ifoodBroker){
            StartedProccess::dispatch($ifoodBroker); //Passo 1
            //break;
        }
        $fim = \time();
        echo $fim - $inicio;        
    }
}
