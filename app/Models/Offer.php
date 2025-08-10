<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    //
    protected $fillable = [
        'img',
        'title',
        'date_time',
        'description',
    ];
    protected $guarded = ['id'];
}