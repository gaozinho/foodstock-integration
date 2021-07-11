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

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
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
        ]                 
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
