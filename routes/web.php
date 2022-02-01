<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Http\Controllers\ImageController;

$router->get('/', function () use ($router) {
    $homepage = file_get_contents('homepage.html');
    return response($homepage, 200);
});

$router->get('api/tokens', 'TokenController@index');
$router->get('api/token/{id}', 'TokenController@show');

$router->get('image/{id}', 'ImageController@show');
$router->get('token/{file}', 'ImageController@old');

$router->get('/test', function(){
    return 'Metadata API2';
});
