<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>帕达控股-密码重置</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="content-type" content="no-cache, must-revalidate" />
    <meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="shortcut icon" href="{{URL::asset('img/favicon.ico')}}?ver={{ resource_version_number() }}" type="image/vnd.microsoft.icon"/>
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/layui/css/layui.css') }}?ver={{ resource_version_number() }}" media="all" />
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/themes/default/easyui.css') }}?ver={{ resource_version_number() }}" media="all" />
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/themes/icon.css') }}?ver={{ resource_version_number() }}" media="all" />
    <script type="text/javascript" src="{{ URL::asset('js/jquery-1.11.3/jquery-1.11.3.min.js') }}?ver={{ resource_version_number() }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/layui/layui.js') }}?ver={{ resource_version_number() }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/jquery.easyui.min.js') }}?ver={{ resource_version_number() }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/formevent/esayui-datagrid-pagination.js') }}?ver={{ resource_version_number() }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/locale/easyui-lang-zh_CN.js') }}?ver={{ resource_version_number() }}"></script>
    <script src="{{ URL::asset('js/live800/openChat4PC.js') }}?ver={{ resource_version_number() }}"></script>
</head>
<style>
    .rou > p {font-size: 14px;padding: 0;line-height: 30px;width: 96%;margin: 0 auto;}
    .rou > h3 {height: 25px;line-height: 25px;font-size: 14px; font-weight:bold; padding: 5px;}
    .header{width:100%;height:85px;background:#23262E;box-shadow:0 -2px 16px -1px #9e9e9e;z-index:99}
    .header>.bx{width:1200px;margin:0 auto;height:85px}
    .header-l{float:left;width:50%;background:#23262E}
    .header-l>p{margin-top:16px}.header-r{float:left;width:50%;text-align:right;background:#23262E}
    .header-r>ul>li{position:relative;display:inline-block;line-height:83px}
    .header-r>ul>li>a{margin:0 30px;font-size:20px}
    .header-r>ul>li:nth-child(4)>a:after{content:"";width:2px;height:30px;display:inline-block;clear:both;position:absolute;left:4px;background:#fff;top:27px}
    .mian{width:600px;height:100%;margin:0 auto;overflow:hidden}
    footer{height:80px;width:100%;background:#23262E;line-height:80px;text-align:center;position:relative;top:50px;}
    footer>.rigth{position:absolute;top:50px;right:0;height:40px;line-height:40px;width:250px;background:#f1c203}
    footer>.rigth>a{display:inline-block;width:100%;height:100%}
    @media screen and (max-width: 768px) {
        .header > .bx {width: 100%;}
        .header-r > ul > li:nth-child(2) {display: none;}
        .header-r > ul > li:nth-child(3){display: none;}
        .header-r > ul > li:nth-child(4){display: none;}
        .min-a{margin-left:10%;}
        .mian{width: 100%;height: 100%;margin: 0px auto;overflow: hidden;}
    }
</style>
<body>
    <div class="header clearfix">
        <div class="bx">
            <div class="header-l">
                <p >
                    <a href="http://{{ Official_web_address() }}" class="min-a" target="_blank"><img src="{{ URL::asset('img/logo.png') }}?ver={{ resource_version_number() }}" alt=""></a>
                </p>
            </div>
            <div class="header-r">
                <ul>
                    <li><a href="http://{{ Official_web_address() }}" style="color:#fff;" target="_blank">官网首页</a></li>
                    <li><a href="javascript:void(0);" onclick="openChat('https://ytpfx.livechatvalue.com/chat/chatClient/chatbox.jsp?companyID=1041184&configID=45162&jid=8125716336&s=1',900,600);"style="color:#fff;">在线客服</a></li>
                    {{--<li><a href="index.html" style="color:#fff;">主页</a></li>
                    <li><a href="Platform-tool.html" style="color: #fff">线上交易工具</a></li>
                    <li><a href="Cooperative-Partner.html" style="color: #fff">合作伙伴</a></li>--}}
                    <li><a href="{{ URL::asset('/') }}" style="color:#ffcd00;">登录</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="mian">
        <form class="layui-form" action="" id="ForgetPasswordForm" style="margin-top: 8px;">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">交易账户</label>
                    <div class="layui-input-block">
                        <input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">手机号</label>
                    <div class="layui-input-block">
                        <input type="text" name="userphoneNo" id="userphoneNo" autocomplete="off" placeholder="请输入手机号" class="layui-input" style="width: 200px;">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-form-item">
                    <label class="layui-form-label">手机验证码</label>
                    <div class="layui-input-inline">
                        <input type="text" id="userverfcode" name="userverfcode" maxlength="6" placeholder="请输入验证码" autocomplete="off" class="layui-input" style="width: 163px;">
                        <button type="button" id="getVerifyCode" onclick="funcGetForgetPswVerifyCode()" class="layui-btn" style="margin-left: 163px;margin-top: -58px;">获取验证码</button>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="button" class="layui-btn" onclick="submintVerifyInfo()">确定</button>
                </div>
            </div>
        </form>

        <div id="changePsw">
            <form class="layui-form" action="" id="ChangePasswordForm" style="margin-top: 8px;">
                <div class="layui-form-item">
                    <label class="layui-form-label">新密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="password" id="password" autocomplete="off" placeholder="请输入新密码" class="layui-input" style="width: 200px;">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">确认密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="againpassword" id="againpassword" autocomplete="off" placeholder="请输入确认密码" class="layui-input" style="width: 200px;">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" onclick="submintChangePsw()">更改密码</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{--<footer>
        <p style=color:#fff;>© 交易和差价合约存在高风险。 损失可能超过投资。</p>
    </footer>--}}
<script src="{{ URL::asset('js/formevent/form.core.js') }}?ver={{ resource_version_number() }}"></script>
<script type="text/javascript">
    $("#changePsw").hide();
    function check_userId() {
        var userId = $.trim($("#userId").val());
        if (userId == "") {
            errorTips("请输入交易账户ID", "msg", "userId");
        } else {
            return true;
        }
    }

    function check_verify_code() {
        var code = $.trim($("#userverfcode").val());
        if (code == "") {
            errorTips("请输入验证码", "msg", "userverfcode");
        } else if (code.length < 6) {
            errorTips("请输入6位有效验证码", "msg", "userverfcode");
        } else {
            return true;
        }
    }

    function funcGetForgetPswVerifyCode() {
        if (!$("#getVerifyCode").hasClass("layui-btn-disabled")) {
            if (check_userId() && userphoneNo()) {
                $.ajax({
                    url: '/user/check_user_info',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        userId:             $.trim($("#userId").val()),
                        userphoneNo:        $.trim($("#userphoneNo").val()),
                        _token:             "{{ csrf_token() }}",
                    },
                    error: function (msg) {
                        layer.msg("网络故障,请稍后再操作", {icon: 5, shift: 6});
                    },
                    success: function (msg) {
                        console.log(msg);
                        if (msg.msg == 'FAIL') {
                            if (msg.err == 'phoneErr') {
                                errorTips('手机号与当前账户绑定手机号不一致!', 'msg', msg.col);
                            } else if (msg.err == 'IDerror') {
                                errorTips('无效的账户ID!', 'msg', msg.col);
                            } else if (msg.err == 'UserDisable') {
                                errorTips('当前账户已被禁用!', 'msg', msg.col);
                            }
                        } else if (msg.msg == 'SUC') {
                            /*验证通过，开始发送验证码*/
                            forgetPswVerifyPassSendCode();
                        }
                    }
                });
            }
        }
    }

    function forgetPswVerifyPassSendCode() {
        if (!$("#getVerifyCode").hasClass("layui-btn-disabled")) {
            var stoptime = 0, countdown = 59, _this = $("#getVerifyCode");
            _this.addClass("layui-btn-disabled");
            _this.html(countdown + "s后可重取");
            //启动计时器，1秒执行一次
            var timer = setInterval(function(){
                if (countdown == 0) {
                    stoptime = 0;
                    clearInterval(timer);//停止计时器
                    _this.removeClass("layui-btn-disabled").html("获取验证码");//启用按钮
                }
                else {
                    countdown--;
                    _this.html( countdown + "s后可重取");
                }
            }, 1000);

            $.ajax({
                url: '/user/forgetpswSendCode',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    userId:             $.trim($("#userId").val()),
                    userphoneNo:        $.trim($("#userphoneNo").val()),
                    _token:             "{{ csrf_token() }}",
                },
                error: function (msg) {
                    countdown = 0;
                    _this.removeClass("layui-btn-disabled").html("获取验证码");//启用按钮
                    layer.msg('网络故障,请稍后操作.');
                },
                success: function (msg) {
                    if (msg.status) {
                        console.log(msg.status);
                        layer.tips('发送成功!', $('#getVerifyCode'));
                    } else {
                        console.log(msg.status);
                        countdown = 0;
                        _this.removeClass("layui-btn-disabled").html("获取验证码");//启用按钮
                        layer.tips('发送失败!', $('#getVerifyCode'));
                    }
                }
            });
        }
    }

    function submintVerifyInfo() {
        if (check_userId() && check_verify_code() && userphoneNo()) {
            var index1 = openLoadShade();
            $.ajax({
                // headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
                url : '/user/forgetPasswordInfoVerification',
                type : "POST",
                dataType : "JSON",
                async: false,
                data : {
                    userId:             $.trim($("#userId").val()),
                    userphoneNo:        $.trim($("#userphoneNo").val()),
                    codedata:           $.trim($("#userverfcode").val()),
                    _token: "{{ csrf_token() }}",
                },
                success : function(data) {
                    closeLoadShade(index1);
                    if (data.msg == 'SUC') {
                        $("#changePsw").append("<input name='accountno' id='accountno' type='hidden' value='"+ $.trim($("#userId").val()) +"' readonly='readonly'>");
                        modifyPswBombBox();
                    } else if (data.msg == "FAIL") {
                        if(data.err == 'errorCodedate') {
                            errorTips("验证码错误!", "msg", msg.col);
                        } else if(data.err == 'PhoneDiff') {
                            errorTips("接收验证码的手机号和输入的手机号不一致", "msg", msg.col);
                        }
                    }
                },
                error : function(data) {
                    closeLoadShade(index1);
                    layer.msg("未知错误, 请联系客服.");
                }
            });
        }
    }

    function submintChangePsw() {
        if (password() && againpassword()) {
            var index1 = openLoadShade();
            $.ajax({
                url : '/user/change_password',
                type : 'POST',
                dataType : 'JSON',
                data : {
                    newPsw:     $.trim($("#password").val()),
                    userId:     $.trim($("#accountno").val()),
                    _token:     "{{ csrf_token() }}",
                },
                async: false,
                success : function(data) {
                    closeLoadShade(index1);
                    if (data.msg == "SUC") {
                        layer.msg("更改成功", {
                            btn: ['知道了'],
                            yes: function (index, layero) {
                                parent.layer.closeAll();
                                top.location = '/';
                            }
                        });
                    } else  if(data.msg == "FAIL"){
                        if (data.err == "neterr") {
                            layer.msg("服务器网络故障, 请稍后重试.");
                        } else if (data.err == "updateerr") {
                            layer.msg("密码重置失败，请稍后重试.");
                        }
                    }
                },
                error : function(data) {
                    closeLoadShade(index1);
                    layer.msg("抱歉，密码修改失败, 请稍后再试.");
                }
            });
        }
    }

    function modifyPswBombBox() {
        var index = layer.open({
            type: 1,
            title: '重置密码',
            skin: 'layui-layer-molv',
            area: ['420px', '240px'],
            move: false,
            content: $('#changePsw')
        });
    }
</script>
</body>
</html>