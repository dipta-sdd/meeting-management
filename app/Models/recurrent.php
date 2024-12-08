<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class recurrent extends Model
{
    //
    protected $fillable = [
        'day',
        'start',
        'end',
        'host',
    ];
}
