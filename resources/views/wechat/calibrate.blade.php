<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <title>宣城配电物联网</title>
    <link rel="stylesheet" href="http://cdn.sg-z.com/css/weui.min.css" type="text/css"/>
    <link rel="stylesheet" href="http://cdn.sg-z.com/css/iotsgcc-min.css" type="text/css"/>
</head>
<body>
<div class="container">
    <div class="page cell">
        <div class="hd">
            <h1 class="page_title">钥匙状态校对</h1>
            <p class="page_desc">请确保钥匙箱内钥匙状态与下面一致</p>
        </div>
        <div class="weui_cells_title"><p>编号名称</p>
            <p style="padding-left:2.2em">当前状态</p>
            <p>状态校正</p></div>
        <div class="weui_cells weui_cells_form" style="text-align: left">
            @foreach($smks as $smk)
                <div>
                    <div>{{sprintf('#%03d %s',$smk->sn,$smk->door)}}</div>
                    <div id="{{$smk->sn}}">{{$smk->missing?'丢失':($smk->state?'借出':'在箱')}}</div>
                    <div><input {{$smk->state?'':'checked'}}/></div>
                </div>
            @endforeach
        </div>
        <div class="weui_toptips weui_warn js_tooltips">对不起，您没有操作权限</div>
    </div>
</div>
<script src="http://cdn.bootcss.com/zepto/1.1.6/zepto.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('.weui_cells_form').children('div').each(function () {
            $(this).attr('class', 'weui_cell weui_cell_switch');
            $(this).children().first().attr('class', 'weui_cell_hd weui_cell_primary').next().attr('class', 'weui_cell_bd weui_cell_primary').next().attr('class', 'weui_cell_ft').children('input').attr({class:'weui_switch',type:'checkbox'});
        });
        $('.weui_switch').click(function(){
            var k = $(this).parent().siblings('.weui_cell_bd');var self = this;
            $.get('/keyscalibrate?sn='+k.attr('id')+'&state='+(self.checked?'0':'1'),function(d){
                if(d==403) {
                    $(self).prop("checked", ! $(self).prop("checked"));
                    var $tooltips = $('.js_tooltips');
                    if ($tooltips.css('display') != 'none') return;
                    $tooltips.show();
                    setTimeout(function () {
                        $tooltips.hide();
                    }, 2000);
                }else {
                    k.html(self.checked?'在箱':'借出');
                }
            });
        });
    });
</script>
</body>
</html>