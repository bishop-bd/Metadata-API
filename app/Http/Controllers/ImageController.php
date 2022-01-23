<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Token;
use Illuminate\Http\Request;
use Laravel\Lumen\Application;
use PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException;
use PHPImageWorkshop\Exception\ImageWorkshopBaseException;
use PHPImageWorkshop\Exception\ImageWorkshopException;
use PHPImageWorkshop\ImageWorkshop;
use stdClass;

class ImageController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createFromTraits($id)
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

        //define layers array
        $layers = [];

        $token = Token::find($id);

        $output = ImageWorkshop::initVirginLayer(2000,2000);

        foreach ($token->attributes as $trait){
             $path = $traitPath . $trait->trait_type . DIRECTORY_SEPARATOR . $trait->value . '.png';
             $output->addLayerOnTop(ImageWorkshop::initFromPath($path));
             $output->mergeAll();
        }

        $output->save($outputLocation, 'test_output.png', true, '000000', 100, false);

        return response($layers);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        //
    }

}
