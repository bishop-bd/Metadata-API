<?php

namespace App\Http\Controllers;

use App\Models\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /**
     * Display a paginated index of tokens.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //check request for per age amount
        $items_per_page = $request->input('items_per_page');

        //if set, use it, if not default to 100
        $items_per_page = ($items_per_page) ? $items_per_page : 100;

        //find token include attributes
        $tokens = Token::with('attributes')->paginate($items_per_page);

        //hide display_type on attributes if it is null
        foreach ($tokens as &$token){
            $token = $this->removeNullDisplayTypes($token);
        }

        //return JSON response
        return response()->json($tokens->toArray());
    }


    /**
     * Display a single token's metadata.
     *
     * @param  \App\Models\Token  $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        //find token
        $token = Token::find($id);
        $token = $this->removeNullDisplayTypes($token);


        //return JSON response
        return response()->json($token->toArray());
    }


    /**
     * @param $token
     * @return mixed
     */
    private function removeNullDisplayTypes($token){
        //hide display_type if null
        foreach ($token->attributes as $attribute){
            if($attribute->display_type === null){
                $attribute->makeHidden('display_type');
            }
        }

        return $token;
    }
}
