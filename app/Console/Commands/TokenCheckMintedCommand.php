<?php

namespace App\Console\Commands;

use App\Models\Token;
use Illuminate\Console\Command;
use Web3\Contract;

class TokenCheckMintedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:checkminted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check smart contract for totalSupply() and sets minted=true up to that number.';

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
        $contract = new Contract(
            config('app.rpc'),
            config('app.abi')
        );

        $contract->at(config('app.contract'))->call('totalSupply', '', function($err, $supply){
            if ($err !== null) {
                $this->info('Error: ' . $err->getMessage());
                return;
            }

            $totalSupply = $supply[0];

            $updated = Token::where([
                ['id', '<=', $totalSupply],
                ['minted', false]
            ])->update(['minted'=>true]);

            $this->info($updated . ' tokens minted since last check.');

        });
    }
}
