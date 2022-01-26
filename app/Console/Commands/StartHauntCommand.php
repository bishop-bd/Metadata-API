<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartHauntCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'haunt:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add dynamic traits to dolls randomly.';

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
