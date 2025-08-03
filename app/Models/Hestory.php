<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hestory extends Model
{
    
    use HasFactory;
    protected $table='hestories';
    protected $guarded=['id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function movie(){
        return $this->belongsTo(Movie::class,'movie_id');
    }
}