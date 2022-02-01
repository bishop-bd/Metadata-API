<?php

namespace App\Console\Commands;

use App\Models\Attribute;
use App\Models\Image;
use App\Models\Token;
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
        //get Haunting attributes -- querying by token is far too intense to work
        $attributes = Attribute::where('trait_type', 'Haunting')->with('tokens')->get();

        //how many tokens have we unhaunted
        $count = 0;

        //for every attribute add its total to the count and loop through it's token
        foreach ($attributes as $attribute) {
            $count += $attribute->tokens->count();

            //for every token of every attribute if haunting is not locked, remove it, else minus one from the count
            foreach ($attribute->tokens as $token) {
                if(!$token->lock_haunt) {
                    $this->info('Token #' . $token->id . ' haunting removed.');

                    $token->attributes()->detach($attribute);

                    $image = Image::where('token_id', $token->id)->orderBy('priority', 'desc')->first();
                    $image->delete();
                }
                else{
                    $count--;
                }
            }
        }

        //output to console how may were unhaunted
        $this->info($count . ' hauntings removed.');
    }
}
