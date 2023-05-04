<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'tmdb_id',
        'kind_id',
    ];

    //many-to-many relationship with Movie model

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }

    //one-to-many relationship with Kind model

    public function kind()
    {
        return $this->belongsTo(Kind::class);
    }
}
