<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //
    protected $fillable =['client_id','latitude','longitude','precision'];

    public function client()
    {
        $this->belongsTo('App\Client');
    }
}
