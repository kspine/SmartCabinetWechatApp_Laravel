<?php

namespace App\Http\Controllers;

use App\Client;
use App\Smartkey;
use EasyWeChat\Foundation\Application as WeChatter;
use Illuminate\Http\Request;

class DeviceConfigController extends Controller
{
    /*
     * Return a Airkiss instruction page to let user config the device's
     * wifi module
     * */
    public function wificonfig()
    {
        $js = (new Wechatter(['js']))['js']
            ->config(["configWXDeviceWiFi"], false, true, true);
        return View('wechat.wificonfigmin', ['js' => $js]);
    }

    /*
     * Match users' fingerprints number with their names
     * */
    public function manage(Request $request)
    {
        $cclient = Client::where('openid', session('wechat_user_id'))->first();
        $pass = false;
        $clients = null;
        if (policy(Client::class)->canSeeClients($cclient)) {
            if ($request->ajax()) {
                $rclient = Client::find($request->input('id'));
                if ($request->input('action') == 'read') return $rclient;
                elseif ($request->input('action') == 'update' &&
                    $cclient->priority > $request->input('priority') &&
                    policy(Client::class)->canUpdateClient($cclient, $rclient)) {
                    if ( $rclient->update($request->except('id'))) return 'SUCCESS';
                }
                return '失败 权限不足';
            }
            $clients = Client::all();
            $pass = true;
        }
        $data = ['clients' => $clients,
            'pass' => $pass,
            'cclient' => $cclient];
        return view('wechat.clientsmanage', $data);
    }

    public function calibrate(Request $request)
    {
        $client = Client::where('openid', session('wechat_user_id'))->first();
        if ($request->ajax()) {
            if (policy(Client::class)->canCalibrateKeys($client)) {
                Smartkey::where('sn', $request->input('sn'))->update(['state' => $request->input('state')]);
                return 200;
            }
            return 403;
        }
        $smks = Smartkey::select('sn', 'door', 'state', 'missing')->get();
        return view('wechat.calibrate', compact('smks', $smks));
    }

}
