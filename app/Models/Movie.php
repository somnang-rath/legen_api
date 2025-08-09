<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    //
    use HasFactory;
    protected $fillable=[
        'movie_image',
        'title_movie',
        'screen_type',
        'genre',
        'duration',
        'release',
        'classification',
        'user_id'
    ];
    protected $table='movies';
    protected $guarded=['id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}