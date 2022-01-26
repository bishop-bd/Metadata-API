<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Token;
use Illuminate\Support\Str;
use PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException;
use PHPImageWorkshop\Exception\ImageWorkshopException;
use PHPImageWorkshop\ImageWorkshop;

class ImageController extends Controller
{

    /**
     * @param $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws ImageWorkshopException
     * @throws ImageWorkshopLayerException
     */
    public function createNewImageFromTraits($id)
    {
        ini_set('max_execution_time', 180);
        //ini_set('memory_limit', '-1');

        //define trait path
        $traitPath = app()->basePath('storage/app/traits/');
        $outputLocation = app()->basePath('storage/app/output/');

        //find last minted token id
        $lastMinted = Token::where('minted', true)->max('id');
        if($id > $lastMinted){
            return response('Fail: No token exists');
        }

        //Get the token
        $token = Token::find($id);

        //create a virgin layer to start
        $output = ImageWorkshop::initVirginLayer(2000,2000);

        //get the trait order from config/app.php
        $traitOrder = config('app.trait_type_order');

        //loop through trait types in order
        foreach ($traitOrder as $traitType){
            //loop through the token's traits
            foreach ($token->attributes as $trait){
                //define a path to the trait's layer image
                $pathToTraitImage = $traitPath . $trait->trait_type . DIRECTORY_SEPARATOR . $trait->value . '.png';

                //if the file exists and this iteration of tokens traits matches the order we are going in
                if($traitType === $trait->trait_type && file_exists($pathToTraitImage)){
                    //add a layer and merge it to cut down on memory usage, then break both loops to avoid wasting cpu
                    $output->addLayerOnTop(ImageWorkshop::initFromPath($pathToTraitImage));
                    $output->mergeAll();
                    continue 2;
                }
            }
        }

        //count the images attached to a token (including deleted), so we can determine a proper priority for the new image
        $imageCount = Image::where('token_id', $token->id)->withTrashed()->count();
        //guid for new image name
        $fileName = Str::uuid()->toString() . '.png';

        //create new image in db attached to token
        $newImage = Image::create([
            'file'=>$fileName,
            'token_id'=>$token->id,
            'priority'=>$imageCount+1
        ]);

        //save image file
        $output->save($outputLocation, $fileName, true, '000000', 100, false);

        //return new image info
        return response($newImage);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //check if token exists and minted if not, abort not found
        $token = Token::find($id);
        if(is_null($token) || !$token->minted){
            abort(404);
        }

        //define storage and determine the highest image priority
        $storageDirectory = app()->basePath('storage/app/images/');
        $image = Image::where('token_id', $id)->orderBy('priority', 'desc')->first();


        //if we haven't downloaded this file before, download it, save it, and return it, otherwise open it and return it
        if(!file_exists($storageDirectory . $image->file)) {
            //set up stream context options to ignore ssl validation
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );

            $fileData = file_get_contents('https://www.dollface.art/token/' . $image->file, false, stream_context_create($arrContextOptions));
            file_put_contents($storageDirectory . $image->file, $fileData);
        }
        else{
            $fileData = file_get_contents($storageDirectory . $image->file);
        }

        //return an image
        return response($fileData, 200, ['Content-Type'=>'image/png']);
    }

}
