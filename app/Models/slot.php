<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class slot extends Model
{
    //
    protected $fillable = [
        'start',
        'end',
        'date',
        'host',
        'booking_id'
    ];
}
