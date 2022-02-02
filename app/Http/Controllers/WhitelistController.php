<?php

namespace App\Http\Controllers;

use App\Models\PublicWhitelist;
use App\Models\Whitelist;
use Illuminate\Http\Request;

class WhitelistController extends Controller
{
    /**
     * @param $wallet
     * @return \Illuminate\Http\JsonResponse
     */
    public function whitelist($wallet){
        $whitelist = Whitelist::where('address', $wallet)->first();

        return response()->json($whitelist);
    }

    /**
     * @param $wallet
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicWhitelist($wallet){
        $whitelist = PublicWhitelist::where('address', $wallet)->first();

        return response()->json($whitelist);
    }
}
