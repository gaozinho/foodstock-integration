<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Foodstock\Bridge\Ifood\Events\PulledEvents;
use App\Foodstock\Bridge\Ifood\Listeners\RequestAndProcessOrders;

use App\Foodstock\Bridge\Ifood\Events\IntegratedOrders;
use App\Foodstock\Bridge\Ifood\Listeners\ConfirmReceiptOrder;

use App\Foodstock\Bridge\Ifood\Events\StartedProccess;
use App\Foodstock\Bridge\Ifood\Listeners\MakePooling;
use App\Foodstock\Bridge\Ifood\Listeners\StartOrderProduction;

use App\Foodstock\Bridge\Ifood\Events\StartedOrderProduction;
use App\Foodstock\Bridge\Ifood\Listeners\SendAcknowledgments;

use App\Foodstock\Bridge\Ifood\Events\CanceledOrders;
use App\Foodstock\Bridge\Ifood\Listeners\CancelProduction;

use App\Foodstock\Bridge\Ifood\Events\ConcludedOrders;
use App\Foodstock\Bridge\Ifood\Listeners\ConcludedProduction;

// NEEMO

use App\Foodstock\Bridge\Neemo\Events\StartedProccess as NeemoStartedProccess;
use App\Foodstock\Bridge\Neemo\Listeners\MakePooling as NeemoMakePooling;

use App\Foodstock\Bridge\Neemo\Events\PulledEvents as NeemoPulledEvents;
use App\Foodstock\Bridge\Neemo\Listeners\RequestAndProcessOrders as NeemoRequestAndProcessOrders;

use App\Foodstock\Bridge\Neemo\Events\IntegratedOrders as NeemoIntegratedOrders;
use App\Foodstock\Bridge\Neemo\Listeners\ConfirmReceiptOrder as NeemoConfirmReceiptOrder;
use App\Foodstock\Bridge\Neemo\Listeners\StartOrderProduction as NeemoStartOrderProduction;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        
        //IFOOD

        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        StartedProccess::class => [
            MakePooling::class,
        ],     

        PulledEvents::class => [
            RequestAndProcessOrders::class,
        ],    
        IntegratedOrders::class => [
            ConfirmReceiptOrder::class,
            StartOrderProduction::class
        ],    

        StartedOrderProduction::class => [
            SendAcknowledgments::class,
        ] ,    

        CanceledOrders::class => [
            CancelProduction::class,
        ], 
        
        ConcludedOrders::class => [
            ConcludedProduction::class,
        ],
        
        //NEEMO

        NeemoStartedProccess::class => [
            NeemoMakePooling::class,
        ],   

        NeemoPulledEvents::class => [
            NeemoRequestAndProcessOrders::class,
        ],

        NeemoIntegratedOrders::class => [
            NeemoConfirmReceiptOrder::class,
            NeemoStartOrderProduction::class
        ],          
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
