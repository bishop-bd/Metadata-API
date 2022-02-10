<?php

namespace App\Console\Commands;

use App\Http\Controllers\TokenController;
use App\Models\Attribute;
use App\Models\Image;
use App\Models\Token;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
        $l1Haunt = $this->selectHauntType(config('app.current_haunt.Common'));
        $l2Haunt = $this->selectHauntType(config('app.current_haunt.Rare'));
        $l3Haunt = $this->selectHauntType(config('app.current_haunt.Unique'));

        //loop through tokens and apply haunts
        $x = 1;
        foreach($tokens as $token){
            $fileName = 'hauntings/' . Str::uuid()->toString() . '.png';

            if($x < 91){
                $level = 1;
                $token->attributes()->attach($l1Haunt);
            }
            elseif($x > 90 && $x < 100){
                $level = 2;
                $token->attributes()->attach($l2Haunt);
            }
            elseif ($x === 100){
                $level = 3;
                $token->attributes()->attach($l3Haunt);
            }

            $newImagePriority = Image::where('token_id', $token->id)->withTrashed()->count();

            $newImage = Image::create([
                'file'=>$fileName,
                'priority'=>$newImagePriority,
                'token_id'=>$token->id
            ]);

            $this->info('Token #' . $token->id . ' haunted. Level: ' . $level . '. Image: ' . $newImage->id);

            $tokenRefresh = TokenController::refreshOpenseaMetadata($token->id);

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
