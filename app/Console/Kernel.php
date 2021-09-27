<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Dotenv\Dotenv;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $loopStart = intval(env('REQUEST_BROKERS_DIVIDE_START', 0));
        $loopEnd = intval(env('REQUEST_BROKERS_DIVIDE_END', 9));

        $all = intval(env('REQUEST_BROKERS_PROCESS_ALL', 0));

        if($all == 1){
            $schedule->exec('curl ' . env('APP_URL') . '/order-processor-parallel')
            ->description("Solicita processamento dos brokers")
            ->everyMinute()
            ->appendOutputTo(public_path("logs" . DIRECTORY_SEPARATOR . "cron-" . date("Y-m-d") . ".log"))
            ->runInBackground();
        }else{
            for($i = $loopStart; $i <= $loopEnd; $i++){
                $schedule->exec('curl ' . env('APP_URL') . '/order-processor-parallel?brokerEndsWith=' . $i)
                    ->description("Solicita processamento dos brokers")
                    ->everyMinute()
                    ->appendOutputTo(public_path("logs" . DIRECTORY_SEPARATOR . "cron-" . $i . "-" . date("Y-m-d") . ".log"))
                    ->runInBackground();
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
