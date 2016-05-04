<?php

namespace App\Http\Controllers;

use App\Client;
use App\Events\ClientSeekServiceEvent;
use App\Jobs\MailAdmin;
use App\Location;
use App\Motionlife\SemanticSearch;
use EasyWeChat\Foundation\Application as WeChat;
use EasyWeChat\Server\Guard;
use Illuminate\Http\Request;
use EasyWeChat\Message\Raw;

class WechatController extends Controller
{
    public function OAuth2()
    {
        session(['wechat_user_id'=>(new Wechat(['oauth']))['oauth']->user()->getId()]);
        return redirect(session('_previous')['url']);
    }

    public function validateMe(Request $request)
    {
        return $request->input("echostr");
    }

    public function processWechat()
    {
        $wechat = new Wechat(['server', 'user']);
        $wechat['server']->setMessageHandler(
            function ($message) use ($wechat) {
                if ($message->MsgType == 'event') {
                    # code...
                    switch ($message->Event) {
                        case 'subscribe':
                            return $this->saveClient($message->FromUserName, $wechat)
                            . '，您好！欢迎关注宣城配电物联网微信公众号。 点击“更多服务-->项目介绍”查看该公众号详细使用说明。';
                        case 'unsubscribe':
                            $this->clientUnfollow($message->FromUserName);
                            break;
                        case 'LOCATION':
                            $this->updateLocation($message);
                            break;
                        case 'CLICK':
                            return $this->menuClicked($message);
                        case 'VIEW':
                            break;
                        case 'kf_close_session':
                        case 'kf_create_session':
                            $this->handleSessionEvent($message);
                            break;
                        //default: return json_encode($message,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                } //Handling Generic Query based on both text and voice
                elseif ($message->MsgType == 'text') return (new SemanticSearch($message->Content, 8))->composeResponse();
                elseif ($message->MsgType == 'voice') return (new SemanticSearch(str_replace('！', '', $message->Recognition), 8))->composeResponse();
                return null;
            }, Guard::VOICE_MSG | Guard::TEXT_MSG | Guard::EVENT_MSG)->serve()->send();
    }

    private function saveClient($openid, $wechat)
    {
        $newClient = Client::firstOrNew(['openid' => $openid]);
        $newClient->follow = 1;
        $chatter = $wechat['user']->get($openid);
        $newClient->nickname = $chatter['nickname'];
        $newClient->save();
        $this->dispatch(new MailAdmin($chatter, 'User Followed'));
        return $newClient->nickname;
    }

    private function clientUnfollow($openid)
    {
        $client = Client::where('openid', $openid)->first()->fill(['follow' => 0]);
        $client->update();
        //Email me about this event
        $this->dispatch(new MailAdmin($client, 'User Unfollowed'));
    }

    private function updateLocation($message)
    {
        $location = new Location();
        $location->client_id = Client::where('openid', $message->FromUserName)->value('id');
        $location->latitude = $message->Latitude;
        $location->longitude = $message->Longitude;
        $location->precision = $message->Precision;
        $location->save();
    }

    private function menuClicked($message)
    {
        $myopenid = $message->ToUserName;
        $clientopenid = $message->FromUserName;
        $reply = null;
        if ('SMART_SERVICE' == $message->EventKey) {
            $consulter = Client::where('openid', $clientopenid)->value('consulter');
            if (!$consulter) {
                $reply = new Raw('<xml><ToUserName><![CDATA[' . $clientopenid . ']]></ToUserName>
                                        <FromUserName><![CDATA[' . $myopenid . ']]></FromUserName>
                                        <CreateTime>' . time() . '</CreateTime>
                                        <MsgType><![CDATA[transfer_customer_service]]></MsgType></xml>');
            }
            event(new ClientSeekServiceEvent($clientopenid, $consulter));
        }
        return $reply;
    }

    private function handleSessionEvent($message)
    {
        $clientopenid = $message->FromUserName;
        $event = $message->Event;
        $kf = $message->KfAccount;
        Client::where('openid', $clientopenid)->first()
            ->update(['consulter' => $event == 'kf_close_session' ? null : $kf]);
        event(new ClientSeekServiceEvent($clientopenid, $event . ':' . $kf));
    }

}