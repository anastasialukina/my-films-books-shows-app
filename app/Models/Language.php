<?php

namespace App\Models;

class Language extends \Illuminate\Database\Eloquent\Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'original_name'
    ];

    //one-to-many relationship with Movie model

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
