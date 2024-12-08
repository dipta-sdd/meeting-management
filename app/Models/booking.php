<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class booking extends Model
{
    //
    protected $fillable = [
        'title',
        'guest',
        'slot_id',
    ];
}
