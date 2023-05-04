<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MovieProductionCompany extends Pivot
{
    protected $fillable = [
        'movie_id',
        'production_company_id',
    ];

    //pivot table for many-to-many relationship between movies and production companies

    public function movie(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function productionCompany(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductionCompany::class);
    }
}
