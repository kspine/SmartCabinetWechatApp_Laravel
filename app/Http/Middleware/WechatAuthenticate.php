<?php

namespace App\Http\Middleware;

use Closure;

class WechatAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //valid signature , option
        if(!$this->checkSignature($request)){
            exit;
        }

        return $next($request);
    }

    private function checkSignature($request)
    {
        $tmpArr = [env('WECHAT_TOKEN','wechat'),$request->input("timestamp"),$request->input("nonce")];
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = sha1( implode( $tmpArr ) );

        return $tmpStr == $request->input("signature");
    }
}
