<?php

namespace App\Http\Middleware;

use Closure;
use EasyWeChat\Foundation\Application as WeChat;

class AuthWechatClients
{
    /**
     * Handle an incoming request from wechat user
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session('wechat_user_id')) return $next($request);
        return (new WeChat(['oauth']))['oauth']->redirect();
    }
}
