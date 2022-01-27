<?php

namespace App\Console\Commands;

use App\Models\Attribute;
use App\Models\Token;
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
        $tokens = Token::where('minted', true)->inRandomOrder()->limit(100)->get();

        //Select haunt traits
        $l1Haunt = $this->selectHauntType('Common');
        $l2Haunt = $this->selectHauntType('Rare');
        $l3Haunt = $this->selectHauntType('Unique');

        //loop through tokens and apply haunts
        $x = 1;
        foreach($tokens as $token){
            if($x < 91){
                $token->attributes()->attach($l1Haunt);
            }
            elseif($x > 90 && $x < 100){
                $token->attributes()->attach($l2Haunt);
            }
            elseif ($x === 100){
                $token->attributes()->attach($l3Haunt);
            }

            $this->info('Token #' . $token->id . ' haunted.');

            $x++;
        }
    }

    /**
     * @param $value
     * @return Attribute
     */
    private function selectHauntType($value){
        //Select Haunt traits, and create them if they don't exist
        $haunt = Attribute::where([
            ['trait_type', 'Haunting'],
            ['value', $value]
        ])->first();

        if(is_null($haunt)) {
            $haunt = Attribute::create([
                'trait_type'=>'Haunting',
                'value'=>$value
            ]);
        }

        return $haunt;
    }
}
