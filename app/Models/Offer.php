<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    //
    protected $fillable = [
        'img',
        'title',
        'price',
        'start_date',
        'description',
    ];
    protected $guarded = ['id'];
}