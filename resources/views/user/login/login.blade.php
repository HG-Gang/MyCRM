<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>客户中心|登录</title>
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
<img class="bgpic" src="{{ URL::asset('img/bglogin.jpg') }}">
<div class="login">
    <h1>客户中心|登录</h1>
    <form class="layui-form">
        <div class="layui-form-item">
            <input class="layui-input" id="loginUid" name="loginUid" placeholder="用户名" lay-verify="required" type="text" autocomplete="off">
        </div>
        <div class="layui-form-item">
            <input class="layui-input" id="loginPassword" name="loginPassword" placeholder="密码" lay-verify="required" type="password" autocomplete="off">
        </div>
        <div class="layui-form-item form_code">
            <input class="layui-input" id="cptcode" name="cptcode" placeholder="验证码" lay-submint lay-filter="subFrom" lay-verify="required" type="text" autocomplete="off">
            <div class="code"><img id="refreshcptcode" src="{{ url ('user/captcha') }}" width="116" height="36"></div>
        </div>
        {{--<div class="layui-form-item remember_me">
            <label class="layui-form-label">记住我</label>
            <div class="layui-input-block">
                <input type="checkbox" name="remember" lay-skin="switch">
            </div>
        </div>--}}
        {{ csrf_field() }}
        <button type="button" class="layui-btn login_btn" lay-submit lay-filter="login">登录</button>
    </form>
   
   
    <div style="font-size: 16px; color: inherit; margin-top:15px; text-align: right;font-size: 14px; color: inherit; margin-top: 28px;">
        <a href="javascript:void(0);" style="color: #fff; margin-right: 20px;" onclick="openChat('https://ytpfx.livechatvalue.com/chat/chatClient/chatbox.jsp?companyID=1041184&configID=45162&jid=8125716336&s=1',900,600);">联系客服</a>
        {{--<a href="javascript:void(0);" style="color: #fff; margin-right: 20px;">联系客服</a>--}}
        <a href="{{ URL::asset('/user/register') }}" style="font-size: 14px; margin-top:15px; margin-right: 20px; text-align: center;color: #fff;" target="_blank">立即注册</a>
        <a href="{{ URL::asset('/user/forget_password') }}" style="font-size: 14px; margin-top:15px; text-align: center;color: #fff;" target="_blank">忘记密码</a>
    </div>
</div>
<script src="{{ URL::asset('js/jquery-1.11.3/jquery-1.11.3.min.js') }}?ver={{ resource_version_number() }}"></script>
{{--<script type="text/javascript" src="{{ URL::asset('js/formevent/formevent.js') }}"></script>--}}
<script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/layui/layui.js') }}?ver={{ resource_version_number() }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/modul/login/login.js') }}?ver={{ resource_version_number() }}"></script>
<script src="{{ URL::asset('js/formevent/form.core.js') }}?ver={{ resource_version_number() }}"></script>
{{--<script>
    $(function () {
        console.log(myBrowserInfo());
        var userAgent=window.navigator.userAgent,
            rMsie=/(msie\s|trident.*rv:)([\w.]+)/,
            rFirefox=/(firefox)\/([\w.]+)/,
            rOpera=/(opera).+version\/([\w.]+)/,
            rChrome=/(chrome)\/([\w.]+)/,
            rSafari=/version\/([\w.]+).*(safari)/;
        function uaMatch(ua)
        {
            var match=rMsie.exec(ua);
            if(match != null)
            {
                return {browser:"IE",version:match[2] || "0"};
            }
            var match=rFirefox.exec(ua);
            if(match != null)
            {
                return {browser:match[1] || "",version:match[2] || "0"};
            }
            var match=rOpera.exec(ua);
            if(match != null)
            {
                return {browser:match[1] || "",version:match[2] || "0"};
            }
            var match=rChrome.exec(ua);
            if(match != null)
            {
                return {browser:match[1] || "",version:match[2] || "0"};
            }
            var match=rSafari.exec(ua);
            if(match != null)
            {
                return {browser:match[2] || "",version:match[1] || "0"};
            }
            if(match != null)
            {
                return {browser:"",version:"0"};
            }
        }
        function init()
        {
            var browser="";
            var version="";
            var browserMatch=uaMatch(userAgent.toLowerCase());
            if(browserMatch.browser)
            {
                browser=browserMatch.browser;
                version=browserMatch.version;
            }
            console.log(browser+"  "+version);
        }

        init();
    });
</script>--}}
</body>
</html>