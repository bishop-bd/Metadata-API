<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['trait_type', 'value'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'pivot'];

    public function tokens(){
        return $this->belongsToMany(Token::class);
    }
}
