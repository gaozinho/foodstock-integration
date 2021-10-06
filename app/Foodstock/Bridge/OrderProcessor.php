<?php
namespace App\Foodstock\Bridge;

use App\Models\IfoodBroker;
use App\Models\NeemoBroker;

use Illuminate\Support\Facades\Log;

class OrderProcessor{
    public function __construct(){
        
    }

    public function start(){
        if(env("SERVER_PROCCESS_IFOOD", true)) $this->startIfood();
        if(env("SERVER_PROCCESS_NEEMO", true)) $this->startNeemo();
    }

    public function startNeemo(){
        Log::info("NEEMO integration - STARTED");
        if(!env("SERVER_PROCCESS_NEEMO")){
            echo "Cant proccess Neemo";
            return;
        }

        $neemoBrokers = NeemoBroker::where("broker_id", 3)
            ->where("enabled", 1)
            ->where("validated", 1)
            ->get();
        $inicio = \time();

        foreach($neemoBrokers as $neemoBroker){
            \App\Foodstock\Bridge\Neemo\Events\StartedProccess::dispatch($neemoBroker); //Passo 1
            //break;
        }
        $fim = \time();
        echo $fim - $inicio;    
    }

    public function startIfood(){
        Log::info("IFOOD integration - STARTED");

        if(!env("SERVER_PROCCESS_IFOOD")){
            echo "Cant proccess Ifood";
            return;
        }

        $ifoodBrokers = IfoodBroker::where("broker_id", 1)
            ->where("enabled", 1)
            ->where("validated", 1)
            ->get();
        $inicio = \time();
        foreach($ifoodBrokers as $ifoodBroker){
            \App\Foodstock\Bridge\Ifood\Events\StartedProccess::dispatch($ifoodBroker); //Passo 1
            //break;
        }
        $fim = \time();
        echo $fim - $inicio;        
    }
}
