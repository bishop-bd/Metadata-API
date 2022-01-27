<?php

namespace App\Console;

use App\Console\Commands\StartHauntCommand;
use App\Console\Commands\TokenImportCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\TokenImportCommand::class,
        Commands\TokenCheckMintedCommand::class,
        Commands\StartHauntCommand::class,
        Commands\StopHauntCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
