<?php

namespace App\Models;

class Language extends \Illuminate\Database\Eloquent\Model
{

    protected $fillable = [
        'name',
    ];

    //one-to-many relationship with Movie model

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
