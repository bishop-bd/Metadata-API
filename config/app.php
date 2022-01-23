<?php

return [

    'token_url' => env('APP_URL', 'localhost:8888') . '/view/',
    'image_base' => env('APP_URL', 'localhost:8888') . '/token/',
    'abi' => file_get_contents(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'abi.txt')),
    'rpc' => env('RPC', 'http://localhost:3334'),
    'contract' => env('CONTRACT_ADDRESS', '0x7Be8076f4EA4A4AD08075C2508e481d6C946D12b')
];
