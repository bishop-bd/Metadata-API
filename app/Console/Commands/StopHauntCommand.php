<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StopHauntCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'haunt:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unlocked haunting traits.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
