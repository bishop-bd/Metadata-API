<?php

namespace App\Console\Commands;

use App\Models\Attribute;
use App\Models\Image;
use App\Models\OldMetaData;
use App\Models\Token;
use Illuminate\Console\Command;

class TokenImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tokens from static json.';

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
        $x = 1;
        while ($x <= 10000){
            $oldMetaData = OldMetaData::find($x);
            $token = json_decode($oldMetaData->json_data);

            //Import Token
            $newToken = Token::create([
                'id'=>$oldMetaData->id,
                'name' => $token->name,
                'description' => 'Haunted Doll Face Token #' . $oldMetaData->id,
                'minted' => (int) $oldMetaData->minted,
                'images' => [Image::create([
                    'file'=>$token->properties->files[0]->uri,
                    'priority'=>0,
                    'token_id'=>$oldMetaData->id
                ])]
            ]);


            //Import token's attributes if not already imported
            foreach ($token->attributes as $attribute){
                $newAttribute = Attribute::where([
                    ['trait_type', $attribute->trait_type],
                    ['value', $attribute->value]
                ])->first();

                if(is_null($newAttribute)){
                    $newAttribute = Attribute::create([
                        'trait_type'=>$attribute->trait_type,
                        'value'=>$attribute->value
                    ]);
                }

                $newToken->attributes()->attach($newAttribute);
            }



            $this->info('Token ' . $newToken->id . ' imported');

            $x++;
        }
    }
}
