<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WiFi配置</title>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <link rel="stylesheet" href="http://cdn.sg-z.com/css/style-min.css" media="screen" type="text/css">
    <script src="http://cdn.bootcss.com/zepto/1.1.6/zepto.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    <script> window.blockUI=function(){$(".ui-blocker").show();};window.unblockUI=function(){$(".ui-blocker").hide();};</script>
</head>

<body>

<div class="pic">
    <div class="wifi"></div>
</div>
<div class="tips">
    <ul>
        <li>1. 请确保手机已连接WiFi</li>
        <li>2. 开机后立刻触摸指纹面板待设备发出配置请求</li>
        <li>3. 当路由的SSID或密码变更后必须重新配置</li>
    </ul>
</div>
<div class="action">
    开始配置
</div>

<script>
    !function(){
        wx.config({!! $js !!});
        wx.ready(function () {
            $('.action').click(function () {
                startAirKiss();
            });
        });

        function startAirKiss(){
            wx.invoke('configWXDeviceWiFi', {}, function(res){
                if(res.err_msg == 'configWXDeviceWiFi:ok'){
                    alert('配置成功!');
                    wx.closeWindow();
                } else {
                    alert('配置失败！请重试');
                }
            });
        }
    }();
</script>
<div class="ui-blocker" style="display:none">
    <div class="ui-blocker-wrapper">
        <div class="ui-blocker-spinner"></div>
    </div>
</div>
</body>

</html>

