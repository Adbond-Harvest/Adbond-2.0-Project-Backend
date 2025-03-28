<?php

namespace app\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use app\Jobs\CheckInvestmentReturns;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Example: Run a job hourly
        \Log::info('Scheduling CheckInvestmentReturns Job');
        $schedule->job(new CheckInvestmentReturns)->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // Or register commands manually
        // $this->commands([
        //     \App\Console\Commands\MyCommand::class,
        // ]);
    }
}
