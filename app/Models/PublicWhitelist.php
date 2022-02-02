<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicWhitelist extends Model
{
    protected $fillable = ['Address', 'R', 'S', 'V'];
}
