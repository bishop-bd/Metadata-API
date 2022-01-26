<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = ['file', 'token_id', 'priority'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function token(){
        return $this->belongsTo(Token::class);
    }
}
