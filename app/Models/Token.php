<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = ['name', 'description', 'minted'];
    protected $hidden = ['minted', 'created_at', 'updated_at', 'images', 'lock_haunt'];
    protected $appends = ['image', 'external_link'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attributes(){
        return $this->belongsToMany(Attribute::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(){
        return $this->hasMany(Image::class);
    }

    /**
     * @return string
     */
    public function getImageAttribute(){
        $image = config('app.image_base') . $this->id;

        return $image;
    }

    /**
     * @return string
     */
    public function getExternalLinkAttribute(){
        return config('app.token_url') . $this->id;
    }
}
