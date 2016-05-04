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
<div class="container js_container"></div>
@if($all)
<script type="text/html" id="tpl_home">
    <div class="page">
        <div class="hd">
            <h1 class="page_title">钥匙状态总览</h1>
            <p class="page_desc">点击对应编号查看详情并进行相关操作<br><i class="weui_icon_success_circle"></i>在箱&nbsp;<i class="weui_icon_waiting_circle"></i>借出&nbsp;<i class="weui_icon_cancel"></i>遗失</p>
        </div>
        <div class="bd">
            <div class="weui_grids">
                @foreach($smartkeys as $smartkey)
                    <a data-k="{{$smartkey->id}}"><p id="n{{$smartkey->id}}">{{sprintf('NO.%03d',$smartkey->sn)}}</p><div><i class="{{$smartkey->missing?2:$smartkey->state}}" id="o{{$smartkey->id}}"></i></div><p id="r{{$smartkey->id}}">{{$smartkey->door}}</p></a>
                @endforeach
            </div>
        </div>
    </div>
</script>
@endif
<script type="text/html" id="tpl_ast">
    <div class="page" style="overflow: hidden">
        <div class="weui_msg">
            <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_{{$all?'':($smk->missing?'warn':($smk->state?'waiting':'success'))}}" id="bic"></i></div>
            <div class="weui_text_area">
                <h1 class="weui_msg_title">钥匙编号：<span id="bsn">{{$all?'':sprintf('NO.%03d',$smk->sn)}}</span></h1>
                <h1 class="weui_msg_title">钥匙名称：<span id="bdr">{{$all?'':$smk->door}}</span></h1>
                <h1 class="weui_msg_title">钥匙状态：<span id="bst">{{$all?'':($smk->missing?'已遗失':($smk->state?'借出待还':'在箱可借'))}}</span></h1>
                <h1 class="weui_msg_title">最后借还：<span id="bsr">{{$all?'':$name}}</span></h1>
                <h1 class="weui_msg_title">操作时间：<span id="btm">{{$all?'':$date}}</span></h1>
                <h1 class="weui_msg_title">预定人员：<span id="bsb">{{$all?'':$subscribers}}</span></h1>
                <p class="weui_msg_desc"><span id="btp"></span></p>
            </div>
            <div class="weui_opr_area">
                <p class="weui_btn_area">
                    <a href="javascript:;" class="weui_btn weui_btn_plain_primary" id="showast">操作选项</a>
                    <a href="javascript:;" class="weui_btn weui_btn_plain_default" id="back">返回</a>
                </p>
            </div>
        </div>
        <div id="toast" style="display: none;">
            <div class="weui_toast">
                <i></i>
                <p class="weui_toast_content"></p>
            </div>
        </div>
        <div class="weui_dialog_confirm" id="dialog1" style="display: none;">
            <div class="weui_dialog">
                <div class="weui_dialog_hd"><strong class="weui_dialog_title">操作确认</strong></div>
                <div class="weui_dialog_bd" id="warnbd"></div>
                <div class="weui_dialog_ft">
                    <a href="javascript:;" class="weui_btn_dialog default" id="cancel">取消</a>
                    <a href="javascript:;" class="weui_btn_dialog primary" id="confirm">确定</a>
                </div>
            </div>
        </div>
        <div id="ast_wrap">
            <div class="weui_mask_transition" id="mask"></div>
            <div class="weui_actionsheet" id="weui_ast">
                <div class="weui_actionsheet_menu" id="menu" style="line-height: 1.3";>
                    <div class="weui_actionsheet_cell{{$all?'':($from=='query'&&$smk->state?'':' dead')}}" id="scb">到箱提醒</div>
                    <div class="weui_actionsheet_cell{{$all?'':($from=='query'&&$smk->state?'':' dead')}}" id="urg">微信催还</div>
                    <div class="weui_actionsheet_cell" id="rpt">遗失上报</div>
                </div>
                <div class="weui_actionsheet_action">
                    <div class="weui_actionsheet_cell" id="ast_cancel">取消</div>
                </div>
            </div>
        </div>
    </div>
</script>
<script src="http://cdn.bootcss.com/zepto/1.1.6/zepto.min.js"></script>
<script src="http://cdn.sg-z.com/js/iotsgcc.min.js"></script>
<script type="text/javascript">
$(function(){
@if($all)
    PM.push(home).push(ast).default('home').init();
    $('.weui_grids a').each(function () {
        $(this).attr({'href': 'javascript:;', 'class': 'weui_grid js_grid', 'data-id': 'ast'});
        $(this).children('p').attr('class', 'weui_grid_label');
        $(this).children('div').attr('class', 'weui_grid_icon').first().children('i').attr('class', function (i, o) {
            return 'weui_icon_' + (o == '2' ? 'cancel' : (o == '1' ? 'waiting_circle' : 'success_circle'));
        });
    });
@else
    home.id = "{{$smk->id}}";
    ast.state = "{{$smk->missing?'miss':($smk->state?'out':'in')}}";
    PM.push(ast).default('ast').init();
@endif
});
</script>
</body>
</html>
