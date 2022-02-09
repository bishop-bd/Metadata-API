<?php

return [

    //Set these in the env file
    'token_url' => env('APP_URL', 'localhost') . '/view/',
    'image_base' => env('APP_URL', 'localhost') . '/image/',
    'rpc' => env('RPC', 'http://localhost:3334'),
    'contract' => env('CONTRACT_ADDRESS', '0x7Be8076f4EA4A4AD08075C2508e481d6C946D12b'),

    //Don't change this unless you know what you are doing.
    'abi' => file_get_contents(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'abi.txt')),

    //Set order for trait types
    'trait_type_order' => [
        'Background',
        'Skin Tone',
        'Skin Texture',
        'Feet',
        'Dress',
        'Dress Texture',
        'Eyes',
        'Mouth',
        'Cheeks',
        'Head',
        'Accessory',
        'Haunting'
    ], //Trait type image layer order, first = bottom layer


    'one_of_ones' => [
        'Cenodoll Labyrinth',
        'Chuck Liddoll',
        'Clown',
        'Cryptofinally',
        'Murder Doll',
        'NFT Crazy',
        'Nightmare',
        'Possesed Artchick',
        'Rebel Joker',
        'Stalker'
    ], //Backgrounds for 1/1s


    'one_of_one_order'=>[
        'Background',
        'Haunting'
    ], //Trait order for 1/1s
];
