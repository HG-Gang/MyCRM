<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <title>后台登录中心|登录</title>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="format-detection" content="telephone=no">
        <link rel="shortcut icon" href="{{ URL::asset('img/favicon.ico')}}?ver={{ resource_version_number() }}" />
        <link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/layui/css/layui.css') }}?ver={{ resource_version_number() }}" media="all" />
        <link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/modul/login/login.css') }}?ver={{ resource_version_number() }}" media="all" />
        <script src="{{ URL::asset('js/live800/openChat4PC.js') }}?ver={{ resource_version_number() }}"></script>
    </head>
    <body>
        <img class="bgpic" src="{{ URL::asset('img/logo/admin_loginbg.jpg') }}?ver={{ resource_version_number() }}">
        <div class="login">
            <h1>后台登录中心|登录</h1>
            <form class="layui-form">
                <div class="layui-form-item">
                    <input class="layui-input" id="loginUid" name="loginUid" placeholder="用户名" lay-verify="required" type="text" autocomplete="off">
                </div>
                <p id="error_uid" style="display: none;margin-top: -9px"><img src="{{URL::asset('img/error.png')}}" width="20"height="20" style="margin-top: -5px"><span style="color: #76dc54;line-height: 18px;"></span><p>
                <div class="layui-form-item">
                    <input class="layui-input" id="loginPassword" name="loginPassword" placeholder="密码" lay-verify="required" type="password" autocomplete="off">
                </div>
                <p id="error_pwd" style="display: none;margin-top: -9px"><img src="{{URL::asset('img/error.png')}}" width="20"height="20" style="margin-top: -5px"><span style="color: #76dc54;line-height: 18px;"></span><p>
                <div class="layui-form-item form_code">
                    <input class="layui-input" id="cptcode" name="cptcode" placeholder="验证码" lay-submint lay-filter="subFrom" lay-verify="required" type="text" autocomplete="off">
                    <div class="code"><img id="refreshcptcode" src="{{ url (route_prefix() . '/captcha') }}" width="116" height="36"></div>
                </div>
                <p id="error_code" style="display: none;margin-top: -9px"><img src="{{URL::asset('img/error.png')}}" width="20"height="20" style="margin-top: -5px"><span style="color: #76dc54;line-height: 18px;"></span><p>
                    {{--<div class="layui-form-item remember_me">
            <label class="layui-form-label">记住我</label>
            <div class="layui-input-block">
                <input type="checkbox" name="remember" lay-skin="switch">
            </div>
        </div>--}}
                    {{ csrf_field() }}
                    <button type="button" class="layui-btn login_btn" lay-submit lay-filter="login" id="but">登录</button>
            </form>
            {{--<h1><div style="font-size: 16px; color: inherit; margin-top:15px; text-align: center;">遇到问题? <a href="https://chat8.live800.com/live800/chatClient/chatbox.jsp?companyID=903401&configID=152019&jid=2958998180&s=1">联系客服</a></div></h1>--}}
            <div style="font-size: 16px; color: inherit; margin-top:15px; text-align: right;font-size: 14px; color: inherit; margin-top: 28px;">
                <a href="javascript:void(0);" style="color: #fff; margin-right: 20px;" onclick="openChat('https://ytpfx.livechatvalue.com/chat/chatClient/chatbox.jsp?companyID=1041184&configID=45162&jid=8125716336&s=1',900,600);">联系客服</a>
                <a href="{{ URL::asset('/user/register') }}" style="font-size: 14px; margin-top:15px; margin-right: 20px; text-align: center;color: #fff;" target="_blank">立即注册</a>
                <a href="{{ URL::asset('/user/forget_password') }}" style="font-size: 14px; margin-top:15px; text-align: center;color: #fff;" target="_blank">忘记密码</a>
            </div>
        </div>

        <script src="{{ URL::asset('js/jquery-1.11.3/jquery-1.11.3.min.js') }}?ver={{ resource_version_number() }}"></script>
        {{--<script type="text/javascript" src="{{ URL::asset('js/formevent/formevent.js') }}"></script>--}}
    <!--<script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/layui/layui.js') }}"></script>-->
    <!--<script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/modul/login/login.js') }}"></script>-->
    <script src="{{asset('/admin/layer/layer.js')}}?ver={{ resource_version_number() }}"></script>
    <script>
    $("#refreshcptcode").click(function () {
        var imgObj = document.getElementById("refreshcptcode");
        imgObj.src = 'captcha?' + Math.random();
        //  $(this).attr('src', 'captcha');
    });
    $(function () {
        $("#but").click(function () {
            var loginUid = $('#loginUid').val();
            var loginPassword = $('#loginPassword').val();
            var cptcode = $('#cptcode').val();
            if (loginUid == "") {
                $('#error_uid').show();
                $('#error_uid').find('span').html('用户名不能为空');
                return false;
            }
            if (loginPassword == "") {
                $('#error_pwd').show();
                $('#error_pwd').find('span').html('密码不能为空');
                return false;
            }
            if (cptcode == "") {
                $('#error_code').show();
                $('#error_code').find('span').html('验证码不能为空');
                return false;
            }
            $.ajax({
                url: "{{url(route_prefix() . '/logon')}}",
                type: "post",
                data: {
                    'loginUid': loginUid,
                    'loginPassword': loginPassword,
                    'cptcode': cptcode,
                    '_token': '{{csrf_token()}}'
                },
                dataType: "json",
                success: function (d) {
                    if (d.state == 1) {
                        layer.load();
                         //此处演示关闭
                        setTimeout(function () {
                            layer.closeAll('loading');
                        }, 1500);
                        window.location.href="{{url(route_prefix() . '/index')}}"
                    } else {
                       layer.msg(d.msg, {time: 2000});
                    }

                }



            });



            return false;



        })

        $('#loginUid').focus(function () {
            $('#error_uid').hide();
            $('#error_uid').find('span').html('');
        })
        $('#loginPassword').focus(function () {
            $('#error_pwd').hide();
            $('#error_pwd').find('span').html('');
        })
        $('#cptcode').focus(function () {
            $('#error_code').hide();
            $('#error_code').find('span').html('');
        })


    })




    </script>
</body>
</html>