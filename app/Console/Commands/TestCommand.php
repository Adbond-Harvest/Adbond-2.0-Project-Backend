<?php

namespace app\Console\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Command;



class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Schedule $schedule)
    {
        $schedule->job(new CheckInvestmentReturns)->everyMinute();
    }
}
