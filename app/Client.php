<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $fillable =['finger_id','name','follow','openid','nickname','priority','consulter',];

    public function locations()
    {
        return $this->hasMany('App\Location');
    }
    public function histories()
    {
        return $this->hasMany('App\History');
    }
    public function isRoot()
    {
        return $this->priority==100;
    }

    public function isAdmin()
    {
        return $this->priority > 79;
    }
}
