<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $fillable = [
        'name',
        'productor',
        'hour',
        'minute',
        'img_film',
        'state',
        'gender_id',
        'user_id'
    ];
}
