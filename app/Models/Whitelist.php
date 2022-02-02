<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Whitelist extends Model
{
    protected $fillable = ['Address', 'R', 'S', 'V'];
}
