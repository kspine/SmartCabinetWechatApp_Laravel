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
            <h1 class="page_title">钥匙借还记录</h1>
            <p class="page_desc">{{$visitor?'您非本系统用户 以下为测试数据':'最近半年记录 使用人：'.$name}}</p>
        </div>
        <div class="bd">
            <div class="weui_cells_title"><p>行为</p><p>钥匙数目</p><p>操作时间</p></div>
            <div class="weui_cells weui_cells_access">
                    @foreach($histories as $history)
                        <div id="{{$history->id}}">
                            <div>{{$history->action?'借':'还'}}</div>
                            <div><p>{{sizeof(explode(':',$history->keysns))}}把</p></div>
                            <div>{{$history->acted_at}}</div>
                        </div>
                    @endforeach
            </div>
        </div>
        <div class="weui_dialog_alert" id="dialog2" style="display: none;">
            <div class="weui_mask"></div>
            <div class="weui_dialog">
                <div class="weui_dialog_hd"><strong class="weui_dialog_title">本次所<span id="jh"></span>钥匙</strong></div>
                <div class="weui_dialog_bd"></div>
                <div class="weui_dialog_ft">
                    <a href="javascript:;" class="weui_btn_dialog primary">确定</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="http://cdn.bootcss.com/zepto/1.1.6/zepto.min.js"></script>
<script type="text/javascript">
$(function(){
    $('.weui_cells_access').children('div').each(function(){
        $(this).attr({'href':'javascript:;','class':'weui_cell'});
        $(this).children().first().attr('class','weui_cell_hd').next().attr('class','weui_cell_bd weui_cell_primary').next().attr('class','weui_cell_ft');
    });
    $('.weui_cell').on('click',function(){
        $('#jh').text($(this).find('div').text());
        var $dbd=$('.weui_dialog_bd');
        $.get('/history?h='+$(this).attr('id'),function(data){$dbd.html(data);});
        var $dialog = $('#dialog2');
        $dialog.show();
        $dialog.find('.weui_btn_dialog').one('click',function(){$dialog.hide();$dbd.html('');});
    });
});
</script>
</body>
</html>
