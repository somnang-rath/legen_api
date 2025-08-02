<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    //
    protected $fillable=[
        'movie_image',
        'title_movie',
        'screen_type',
        'genre',
        'duration',
        'release',
        'classification'
    ];
}