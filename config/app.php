<?php

return [

    'token_url' => env('APP_URL', 'localhost:8888') . '/view/',
    'image_base' => env('APP_URL', 'localhost:8888') . '/token/',
    'abi' => file_get_contents(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'abi.txt')),

];
