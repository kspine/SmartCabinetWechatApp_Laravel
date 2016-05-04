<?php

namespace App\Listeners;

use App\Events\ClientSeekServiceEvent;
use EasyWeChat\Foundation\Application as Wechat;

class MollifyClientListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ClientSeekService  $event
     * @return void
     */
    public function handle(ClientSeekServiceEvent $event)
    {
        $message = $event->message;
        if(str_contains($message,'kf_close_session'))
        {
            $reply = "咨询结束,祝您生活愉快!";
        }elseif(str_contains($message,'kf_create_session'))
        {
            $kfAcount = explode(':',$message)[1];
            $reply="客服{$kfAcount} 已接入，主动结束对话请再次点击技术咨询菜单。";
        }
        elseif(str_contains($message,'@'))//close session for him
        {
            (new Wechat(['staff']))['staff']->close($message,$event->openid,'用户主动关闭会话');
            return;

        }else//notify him hang on a second.
        {
            $reply = "正在为您转接技术人员，请稍后...[咖啡]";
        }
        (new Wechat(['staff']))['staff']->message($reply)->to($event->openid)->send();
    }
}
