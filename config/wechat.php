<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/14/2016
 * Time: 10:29 AM
 */
return [
    /**
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug'  => env('WECHAT_DEBUG',false),

    /**
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'app_id'  => env('WECHAT_APP_ID'),           // AppID
    'secret'  => env('WECHAT_SECRET'),          // AppSecret
    'token'   => env('WECHAT_TOKEN'),           // Token
    'aes_key' => env('WECHAT_AES_KEY'),         // EncodingAESKey

    /**
     * 日志配置
     *
     * level: 记录的级别,\Monolog\Logger 常量，可选为：
     *         debug/info/notice/warning/error/critical/alert/emergency
     * file：日志文件位置，要求可写权限
     */
    'log' => [
        'level' => 'debug',
        'file'  => '/Motionlife/tmp/easywechat.log',
    ],

    /**
     * OAuth 配置
     *
     * scopes：公众平台（snsapi_base/snsapi_userinfo），开放平台：snsapi_login
     * callback：OAuth授权完成后的回调页地址
     */
    'oauth' => [
        'scopes'   => ['snsapi_base'],
        'callback' => '/oauth2callback',
    ],
    // more...
];