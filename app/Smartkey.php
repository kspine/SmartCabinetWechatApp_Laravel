<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Smartkey extends Model
{
    //
    protected $fillable = [
        'sn', 'door','state','finger_id','subscribers','missing','inout_time',
    ];

    public function getFingerName()
    {
        return Client::where('finger_id',$this->finger_id)->value('name');
    }
    public function getSubscriberNames()
    {
        $c_ids = explode(':',$this->subscribers);
        $names =[];
        foreach($c_ids as $id)
        {
            $name = Client::where('id',$id)->value('name');
            if($name) array_push($names,$name);
        }
        return $names;
    }

}
