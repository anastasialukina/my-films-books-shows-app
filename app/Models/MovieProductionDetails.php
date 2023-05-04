<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieProductionDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'country_id',
        'status_id'
    ];

    //one-to-one relationship with Movie model

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
