/**
 * Created by Motionlife 1/27/2016
 **/
var PM = {
    $container: $('.js_container'),
    _pageStack: [],
    _configs: [],
    _defaultPage: null,
    default: function (name) {
        this._defaultPage = name;
        return this;
    },
    init: function () {
        var self = this;
        self.go(self._defaultPage);
        return this;
    },
    push: function (config) {
        this._configs.push(config);
        return this;
    },
    go: function (to) {
        var config = this._find('name', to);
        if (!config) return;
        if (!config.isBind) this._bind(config);
        var html = $(config.template).html();
        var $html = $(html).addClass('slideIn').addClass(config.name);
        this.$container.append($html);
        this._pageStack.push({config: config, dom: $html});
        return this;
    },
    back: function () {
        var outpage = this._pageStack.pop();
        if (!outpage)  return;
        outpage.dom.addClass('slideOut').on('animationend', function () {
            outpage.dom.remove();
        }).on('webkitAnimationEnd', function () {
            outpage.dom.remove();
        });
        if (this._pageStack.length == 0) WeixinJSBridge.call('closeWindow');
        else return this;
    },
    _find: function (key, value) {
        var page = null;
        for (var i = 0, len = this._configs.length; i < len; i++) {
            if (this._configs[i][key] === value) {
                page = this._configs[i];
                break;
            }
        }
        return page;
    },
    _bind: function (page) {
        var events = page.events || {};
        for (var t in events) {
            for (var type in events[t]) {
                this.$container.on(type, t, events[t][type]);
            }
        }
        page.isBind = true;
    }
};

var home = {
    name: 'home',
    template: '#tpl_home',
    events: {
        '.js_grid': {
            click: function () {
                var id = $(this).data('id');
                home.id = parseInt($(this).data('k'));
                PM.go(id);
                passData(home.id);
                $.ajax({
                    type: 'GET',
                    url: '/smartkey/' + home.id,
                    dataType: 'json',
                    success: function (data) {
                        $('#bsr').html(data.name);
                        $('#btm').html(data.date);
                        $('#bsb').html(data.subscribers);
                    }
                });
                ast.tgbtn();
                function passData(id) {
                    $('#bsn').html($('#n' + id).text());
                    $('#bdr').html($('#r' + id).text());
                    $('#bic').attr('class', function () {
                        var str = $('#o' + id).attr('class');
                        var st, tip, ic = '';
                        if (str.indexOf('success') > 0) {
                            ast.state = 'in';
                            st = '在箱可借';
                            tip = '可到钥匙箱通过指纹验证提取使用';
                            ic = 'weui_icon_success';
                        } else if (str.indexOf('wait') > 0) {
                            ast.state = 'out';
                            st = '借出待还';
                            tip = '点击操作选项可预定归还提醒或主动催还';
                            ic = 'weui_icon_waiting';
                        } else {
                            ast.state = 'miss';
                            st = '已遗失';
                            tip = '钥匙找回后请及时联系管理员更新';
                            ic = 'weui_icon_warn';
                        }
                        $('#bst').html(st);
                        $('#btp').html(tip);
                        return 'weui_icon_msg ' + ic;
                    });
                }
            }
        }
    }
};

var ast = {
    hideast: function (weuiast, mask) {
        $('#dialog1').hide();
        weuiast.removeClass('weui_actionsheet_toggle');
        mask.removeClass('weui_fade_toggle');
        weuiast.on('transitionend', function () {
            mask.hide();
        }).on('webkitTransitionEnd', function () {
            mask.hide();
        });
    },
    tgbtn: function () {
        $('#scb').toggleClass(this.state == 'out' ? '' : 'dead');
        $('#urg').toggleClass(this.state == 'out' ? '' : 'dead');
        $('#rpt').toggleClass(this.state == 'miss' ? 'dead' : '');
    },
    name: 'ast',
    template: '#tpl_ast',
    events: {
        '#showast': {
            click: function () {
                var mask = $('#mask');
                var weuiast = $('#weui_ast');
                weuiast.addClass('weui_actionsheet_toggle');
                mask.show().addClass('weui_fade_toggle').one('click',function () {
                    ast.hideast(weuiast, mask);
                });
                $('#ast_cancel').one('click',function () {
                    ast.hideast(weuiast, mask);
                });
                weuiast.unbind('transitionend').unbind('webkitTransitionEnd');
            }
        },
        '#back': {
            click: function () {
                ast.tgbtn();
                PM.back();
            }
        },
        '#menu .weui_actionsheet_cell': {
            click: function () {
                var $dialog = $('#dialog1');
                if ($(this).attr('class').indexOf('dead') > 0 || $dialog.css('display') != 'none') return;
                var rq = $(this).attr('id');
                $('#warnbd').text(rq == 'scb' ? '登记成功后，该钥匙到箱时公众号会给您发送一条提取通知' : (rq == 'urg' ? '通过本公众号给该钥匙的借用者发送一条催还提醒' : '向管理员报告登记该钥匙已丢失'));
                $dialog.show();
                $('#confirm').one('click', function () {
                    $.ajax({
                        type: 'POST',
                        url: '/smartkey/' + home.id + '?_method=put&rq=' + rq,
                        dataType: 'json',
                        success: function (d) {
                            stot(d);
                        },
                        error: function () {
                            stot({msg: '操作失败', result: 404});
                        }
                    });
                    ast.hideast($('#weui_ast'), $('#mask'));
                });
                $('#cancel').one('click', function () {
                    ast.hideast($('#weui_ast'), $('#mask'));
                });
                function stot(d) {
                    var $tot = $('#toast');
                    if ($tot.css('display') != 'none') return;
                    $tot.find('i').attr('class', d.result == 200 ? 'weui_icon_toast' : (d.result == 403 ? 'weui_icon_safe_warn' : 'weui_icon_warn'));
                    $tot.find('p').text(d.msg);
                    $tot.show();
                    setTimeout(function () {
                        $tot.hide();
                    }, 2000);
                    if (d.ps != '') $('#bsb').html(function (i, o) {
                        if (o.substr(0, 1) == '无') o = o.substr(1);
                        return o + ' ' + d.ps;
                    });
                }
            }
        }
    }
};