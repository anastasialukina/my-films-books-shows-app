<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'tagline',
        'release_date',
        'runtime',
        'poster_path',
        'tmdb_id',
        'vote_average',
        'vote_count',
        'original_language_id',
        'imdb_id',
    ];


    //many-to-many relationship with Genre model

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    //one-to-many relationship with Language model

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    //one-to-one relationship with MovieProductionDetails model

    public function movieProductionDetails()
    {
        return $this->hasOne(MovieProductionDetails::class);
    }

    //many-to-many relationship with ProductionCompany model
    public function productionCompanies()
    {
        return $this->belongsToMany(ProductionCompany::class, 'movie_production_company');
    }
}
