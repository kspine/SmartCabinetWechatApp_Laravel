<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/', 'PublicController@homePage');
Route::get('about','PublicController@aboutPage');

//In order to let it load asap to boost the user experience, we don't add any middleware
Route::get('wificonfig','DeviceConfigController@wificonfig');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::group(['middleware' => ['web']], function () {

    Route::auth();

    Route::get('home', 'HomeController@index');

    //Here we only want to do business with our wechat followers who are also our clients
    //SO we need to auth their basic info before we can provide our service to them
    Route::group(['middleware' => ['oauth2']], function () {

        Route::get('keyscalibrate','DeviceConfigController@calibrate');

        Route::get('clientsmanage','DeviceConfigController@manage');

        Route::resource('smartkey','SmartkeyController',['only'=>['update','index','show']]);

        Route::get('history','HistoryController@index');
    });

    //Process the grant result from wechat authorization server
    Route::get('oauth2callback','WechatController@OAuth2');
});

/*
|--------------------------------------------------------------------------
| 接受微信服务器发来的消息和事件推送
|--------------------------------------------------------------------------
|
| 用户每次向公众号发送消息、或者产生自定义菜单点击事件时，该URL将得到所有的微信服
| 务器推送过来的消息和事件，然后开发者可以依据自身业务逻辑进行响应，例如回复消息等。
| 安全性检查在WechatMiddleWare中实现
|
*/
Route::get('wechat', ['middleware' => 'wechat', 'uses'=>'WechatController@validateMe']);
Route::post('wechat', ['middleware' => 'wechat', 'uses'=>'WechatController@processWechat']);

/*
|--------------------------------------------------------------------------
| IoTSafeCase Routes
|--------------------------------------------------------------------------
|
| This route is for proceses data send from the smart case, we'll use a very
| clever way to encript this communication to make it safer, which will be implemented
|  in record middleware
|
*/
Route::post('record',['middleware' => 'record', 'uses'=>'RecordController@processRecord']);
