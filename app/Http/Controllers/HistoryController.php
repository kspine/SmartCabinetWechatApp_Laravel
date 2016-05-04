<?php

namespace App\Http\Controllers;

use App\Client;
use App\History;

use App\Http\Requests;
use App\Smartkey;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $client = Client::where('openid',session('wechat_user_id'))->first();
        if(is_null($client)||$client->priority==0){
            $client = Client::find(3);//Let the unregistered user view as a test client
            $visitor = true;
        }
        $whereclause = 'id ='.$request->input('h').' and client_id='.$client->id;
        if(policy(Client::class)->canViewAllHistory($client)){
            if(($ocid = $request->input('ocid'))>0 ) $client = Client::find($ocid);
            $whereclause = 'id ='.$request->input('h');
        }
        if($request->ajax()){
            $content='';
            $sns = explode(':',History::whereRaw($whereclause)->value('keysns'));
            foreach($sns as $sn)
            {
                $door= Smartkey::where('sn',$sn)->value('door');
                $sn=sprintf('#%03d',$sn);
                $content = $content."<div>{$sn} {$door}</div>";
            }
            return $content;
        }
        $data=[
            'name'=>$client->name,
            'histories'=>$client->histories()
                ->where('acted_at','>=',date('Y-m-d', strtotime(' -180 day')))
                ->orderBy('acted_at', 'desc')->get(),
            'visitor'=>isset($visitor),
        ];
        return view('wechat.historymin',$data);//abort(403);
    }

}
