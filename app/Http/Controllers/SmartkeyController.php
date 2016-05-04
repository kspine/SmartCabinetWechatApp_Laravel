<?php

namespace App\Http\Controllers;

use App\Client;
use App\Jobs\MailAdmin;
use App\Jobs\RemindHolder;
use App\Smartkey;
use Illuminate\Http\Request;
use Exception;

class SmartkeyController extends Controller
{
    //show all the keys
    public function index()
    {
        $smartkeys = Smartkey::all();
        $data = ['smartkeys' => $smartkeys, 'all' => true];
        return view('wechat.smartkeymin', $data);
    }

    //show the key specified by id
    public function show($id, Request $request)
    {
        $smtkey = Smartkey::find($id);
        $json = ['name' => $smtkey->getFingerName() ?: '未知',
            'date' => $smtkey->inout_time,
            'subscribers' => implode(' ', $smtkey->getSubscriberNames()) ?: '无'];
        if ($request->ajax()) return $json;
        $from = $request->input('from', 'index');
        return view('wechat.smartkeymin', array_merge($json, ['smk' => $smtkey, 'all' => false, 'from' => $from]));
    }

    //manipulation of smartkeys
    public function update($id, Request $request)
    {
        $client = Client::where('openid', session('wechat_user_id'))->first();
        $key = Smartkey::findOrFail($id);
        if (policy(Client::class)->canUseWechat($client)) {
            return $this->updateKey($key, $request->input('rq'), $client);
        }
        return ['result' => 403, 'msg' => '无操作权限', 'ps' => ''];
    }

    private function updateKey($key, $code, $client)
    {
        $ps = '';
        $msg = 'FAIL';
        switch ($code) {
            case 'scb'://book this key
                $cid = $client->id;
                $scbrs = explode(':', $key->subscribers);
                if (in_array($cid, $scbrs)) {
                    $msg = '已登记过';
                } elseif ($client->finger_id == $key->finger_id) {
                    $msg = '无需提醒自己!';
                } else {
                    array_push($scbrs, $cid);
                    $key->subscribers = implode(':', $scbrs);
                    try {
                        $key->save();
                        $msg = '登记成功';
                        $ps = $client->name;
                    } catch (Exception $e) {
                        $msg = '操作失败!';
                    }
                }
                break;
            case 'urg'://urge the holder of the key
                if ($client->finger_id == $key->finger_id) {
                    $msg = '您就是借用者!';
                } else {
                    dispatch(new RemindHolder($key));
                    $msg = '发送成功';
                }
                break;
            case 'rpt'://report missing
                $this->dispatch(new MailAdmin($client, 'Report Key Missing:#' . $key->id));
                $msg = '上报成功';
                break;
        }
        return ['result' => 200, 'msg' => $msg, 'ps' => $ps];
    }
}
