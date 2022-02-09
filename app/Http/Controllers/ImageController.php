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

        //define trait path
        $outputLocation = app()->basePath('storage/app/images/');

        //find last minted token id
        $lastMinted = Token::where('minted', true)->max('id');
        if($id > $lastMinted){
            return response('Fail: No token exists');
        }

        //Get the token
        $token = Token::find($id);

        $output = $this->generateImageFromTraits($token);

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
     * @param Token $token
     * @return \PHPImageWorkshop\Core\ImageWorkshopLayer
     * @throws ImageWorkshopException
     */
    private function generateImageFromTraits(Token $token){
        //define trait path
        $traitPath = app()->basePath('storage/app/traits/');

        //create a virgin layer to start
        $output = ImageWorkshop::initVirginLayer(2000,2000);

        //get the trait order from config/app.php
        $traitOrder = (in_array($token->background, config('app.one_of_ones'))) ? config('app.trait_type_order') : config('app.one_of_one_order');

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

        return $output;
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
            //if files does not exist regenerate from traits
            $output = $this->generateImageFromTraits($token);

            //save image file
            $output->save($storageDirectory, $image->file, true, '000000', 100, false);

            //old code to copy images form the old server for testing purposes
            //set up stream context options to ignore ssl validation
//            $arrContextOptions=array(
//                "ssl"=>array(
//                    "verify_peer"=>false,
//                    "verify_peer_name"=>false,
//                ),
//            );
//
//            $fileData = file_get_contents('https://www.dollface.art/token/' . $image->file, false, stream_context_create($arrContextOptions));
//            file_put_contents($storageDirectory . $image->file, $fileData);
        }

        //acquire file contents
        $fileData = file_get_contents($storageDirectory . $image->file);

        //set mime type to mp4 or png
        $contentType = (substr($image->file, -4) === '.mp4') ? 'video/mp4' : 'image/png';

        //return an image
        return response($fileData, 200, ['Content-Type'=>$contentType]);
    }

    /**
     * Provides compatibility with old api image structure for seamless transition as well as image access by filename
     * @param $file
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function old($file){
        $storageDirectory = app()->basePath('storage/app/images/');

        if(!file_exists($storageDirectory . $file)){
            abort(404);
        }

        //
        $fileData = file_get_contents($storageDirectory . $file);

        //return an image
        return response($fileData, 200, ['Content-Type'=>'image/png']);
    }
}
