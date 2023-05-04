<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'iso_3166_1',
        'name',
    ];

    //one-to-many relationship with ProductionCompany model

    public function productionCompanies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductionCompany::class, 'origin_country_id');
    }
}
