<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MovieGenre extends Pivot
{
    //movie genre pivot table

    protected $table = 'movie_genre';

    protected $fillable = [
        'movie_id',
        'genre_id',
    ];

    //this table is the pivot table for the many-to-many relationship between the Movie and Genre models

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
