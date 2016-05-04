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
@if($pass)
    <div class="container">
        <div class="page cell">
            <div class="hd">
                <h1 class="page_title">用户管理</h1>
                <p class="page_desc">设置用户真实姓名、指纹ID以及系统权限</p>
            </div>
            <div class="weui_cells">
                <div class="weui_cells_title">
                   请根据微信昵称选择特定用户
                </div>
                <div class="weui_cell weui_cell_select weui_select_after">
                    <div class="weui_cell_hd">
                        微信昵称
                    </div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <select class="weui_select" name="select2">
                            @foreach($clients as $client)
                                @if($client->follow)
                                    <option value="{{$client->id}}" {{$client->id==$cclient->id?'selected':''}}>{{$client->nickname}}({{$client->name}})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="weui_cells_title" id="openid" style="font-size: .7em">OPENID:{{$cclient->openid}}</div>
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">姓名</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" id="name" value="{{$cclient->name}}"/>
                    </div>
                </div>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">指纹ID</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="tel" id="finger_id" value="{{$cclient->finger_id}}"/>
                    </div>
                </div>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">权限值</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="tel" id="priority" value="{{$cclient->priority}}"/>
                    </div>
                </div>
            </div>
            <div class="weui_cells_tips">请仔细核对无误后再确认提交</div>
            <div class="weui_btn_area" style="margin-bottom:.8em">
                <a class="weui_btn weui_btn_primary" href="javascript:" id="updateclient">确定提交</a>
                <a href="/history?ocid={{$cclient->id}}" class="weui_btn weui_btn_plain_primary" id="viewhistory">看其借还记录</a>
            </div>
            <div id="toast" style="display: none;">
                <div class="weui_toast">
                    <i></i>
                    <p class="weui_toast_content"></p>
                </div>
            </div>
            <div class="weui_dialog_confirm" id="dialog1" style="display: none;">
                <div class="weui_mask"></div>
                <div class="weui_dialog">
                    <div class="weui_dialog_hd"><strong class="weui_dialog_title">提交</strong></div>
                    <div class="weui_dialog_bd" id="warnbd">是否确认更新用户资料</div>
                    <div class="weui_dialog_ft">
                        <a href="javascript:;" class="weui_btn_dialog default" id="cancel">取消</a>
                        <a href="javascript:;" class="weui_btn_dialog primary" id="confirm">确定</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="http://cdn.bootcss.com/zepto/1.1.6/zepto.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('.weui_select').change(function(){
                var id= $('option').not(function(){ return !this.selected }).val();
                $.get('/clientsmanage?action=read&id='+id,function(d){
                    $('#openid').text('OPENID:'+d.openid);$('#name').val(d.name);$('#finger_id').val(d.finger_id);
                    $('#priority').val(d.priority);
                });
                $('#viewhistory').attr('href','http://iot.sg-z.com/history?ocid='+id);
            });
            $('#updateclient').on('click', function () {
                $('#dialog1').show();
            });
            $('#confirm').on('click', function () {
                var id= $('option').not(function(){ return !this.selected }).val()
                var data='&name='+$('#name').val()+'&finger_id='+$('#finger_id').val()+'&priority='+$('#priority').val();
                $.get('/clientsmanage?action=update&id='+id+data,function(d){
                    var $t=$('#toast');$('.weui_toast_content').text(d);
                    $('.weui_toast').children('i').attr('class', d=='SUCCESS'?'weui_icon_msg weui_icon_success_no_circle':'weui_icon_msg weui_icon_cancel');
                    $t.show();
                    setTimeout(function(){$t.hide();},2000);
                });
                $('#dialog1').hide();
            });
            $('#cancel').on('click', function () {
                $('#dialog1').hide();
            });
        });
    </script>
@else
    <div class="container">
        <div class="page cell">
            <div class="hd">
                <h1 class="page_title">无权限</h1>
            </div>
            <div class="bd spacing">
                <i class="weui_icon_safe weui_icon_safe_warn" style="margin-left:33.33%;font-size:10em"></i>
            </div>
        </div>
    </div>
@endif
</body>
</html>