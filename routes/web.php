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
    return 'Metadata AP2';
});

$router->get('/tokens', 'TokenController@index');
$router->get('/token/{id}', 'TokenController@show');

$router->get('/image/{id}', 'ImageController@show');

$router->get('/test', function(){
    return 'Metadata API2';
});
