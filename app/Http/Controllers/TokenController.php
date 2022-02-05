<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
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
        //check request for per page amount
        $items_per_page = $request->input('items_per_page');

        //if set, use it, if not default to 100
        $items_per_page = ($items_per_page) ? $items_per_page : 100;

        //find token include attributes, must be before last minted token
        $tokens = Token::with('attributes')
            ->where('id', '<=', $this->lastMinted())
            ->paginate($items_per_page);

        //hide display_type on attributes if it is null
        foreach ($tokens as &$token){
            $token = $this->removeNullDisplayTypes($token);
        }

        //return JSON response
        return response()->json($tokens->toArray(), 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return mixed
     */
    public static function lastMinted(){
        //returns id of last token minted
        return Token::where('minted', true)->max('id');
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

        if(!$token->minted){
            return $this->returnError('token not found');
        }

        //remove null display types
        $token = $this->removeNullDisplayTypes($token);

        //return JSON response
        return response()->json($token->toArray(), 200, [], JSON_UNESCAPED_SLASHES);
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

    /**
     * @param $msg
     * @return \Illuminate\Http\JsonResponse
     */
    private function returnError($msg){
        return response()->json(['error'=>$msg]);
    }


    /**
     * @param $token
     * @return mixed
     */
    public static function refreshOpenseaMetadata($token){
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $refresh = file_get_contents('https://api.opensea.io/asset/' . config('app.contract') . '/' . $token . '?force_update=true', false, stream_context_create($arrContextOptions));

        return json_decode($refresh);
    }
}
