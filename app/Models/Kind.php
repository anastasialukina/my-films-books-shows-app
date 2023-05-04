<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kind extends Model
{
    use HasFactory;

    public $kinds = [
        'movie',
        'tv',
        'book',
        'game',
        'anime',
    ];

    protected $fillable = [
        'name',
    ];


    //one-to-many relationship with Genre model
    public function genres()
    {
        return $this->hasMany(Genre::class);
    }
}
