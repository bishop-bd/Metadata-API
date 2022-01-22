<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['file', 'token_id', 'priority'];

    public function token(){
        return $this->belongsTo(Token::class);
    }
}
