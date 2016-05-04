<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = [
        'finger_id', 'state_a','state_b','state_c','state_d','acted_at',
    ];
}
