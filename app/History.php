<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'client_id', 'action','keysns','acted_at',
    ];
    public function client()
    {
        $this->belongsTo('App\Client');
    }
}
