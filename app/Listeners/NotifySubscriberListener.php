<?php

namespace App\Listeners;

use App\Client;
use App\Events\SmartkeyBackEvent;
//use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use EasyWeChat\Foundation\Application as WeChat;

class NotifySubscriberListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    const TEMPLATE_ID = 'wnkEgaVK3ZIOUPj2-ks0hknaVvfXurIUwkQV13zoIZQ';
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  SmartkeyBackEvent  $event
     * @return void
     */
    public function handle(SmartkeyBackEvent $event)
    {
        $c_ids = explode(':',$event->client_ids);
        $wechatter = (new Wechat(['notice']))['notice'];
        foreach($c_ids as $id)
        {
            $openid = Client::where('id',$id)->value('openid');
            if($openid)
            {
                $data=['first'=>"您好，您预定的智能钥匙现已归还到箱\n",
                        'keyword1'=>'#'.sprintf('%03d',$event->sn),
                        'keyword2'=>$event->door,
                        'keyword3'=>date('Y-m-d H:i:s', time()),
                        'remark'=>"\n请您到钥匙箱通过指纹验证提取使用，谢谢！"];
                $messageId = $wechatter->withTo($openid)
                            ->withUrl('http://iot.sg-z.com/smartkey/'.$event->sn.'?from=notify')
                            ->withColor('#00cc00')
                            ->withTemplate(NotifySubscriberListener::TEMPLATE_ID)
                            ->withData($data)->send();
            }
        }
    }
}
