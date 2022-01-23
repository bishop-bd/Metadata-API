<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = ['name', 'description', 'minted'];
    protected $hidden = ['minted', 'created_at', 'updated_at', 'images'];
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
        $maxPriority = $this->images->max('priority');
        $image = config('app.image_base') . $this->images->where('priority', $maxPriority)->first()->file;

        return $image;
    }

    /**
     * @return string
     */
    public function getExternalLinkAttribute(){
        return config('app.token_url') . $this->id;
    }
}
