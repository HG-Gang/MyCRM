<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>帕达控股|客户中心</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="shortcut icon" href="{{URL::asset('img/favicon.ico')}}?ver={{ resource_version_number() }}" type="image/vnd.microsoft.icon"/>
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/layui/css/layui.css') }}?ver={{ resource_version_number() }}" media="all" />
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/modul/index/index.css') }}?ver={{ resource_version_number() }}" media="all" />
    <script src="{{ URL::asset('js/live800/openChat4PC.js') }}?ver={{ resource_version_number() }}"></script>
</head>
<body class="main_body">
<div class="layui-layout layui-layout-admin">
    <!-- 顶部 -->
    <div class="layui-header header">
        <div class="layui-main">
            <a href="http://{{ Official_web_address() }}" class="logo" target="_blank"><img src="{{ URL::asset('img/logo.png') }}?ver={{ resource_version_number() }}" alt="" style="margin-left: -20px;"></a>
            <!-- 显示/隐藏菜单 -->
            <a href="javascript:;" class="hideMenu"><i class="layui-icon">&#xe638;</i></a>
            <!-- 天气信息 -->
            <div class="weather" pc>
                <div id="tp-weather-widget"></div>
                <script>(function(T,h,i,n,k,P,a,g,e){g=function(){P=h.createElement(i);a=h.getElementsByTagName(i)[0];P.src=k;P.charset="utf-8";P.async=1;a.parentNode.insertBefore(P,a)};T["ThinkPageWeatherWidgetObject"]=n;T[n]||(T[n]=function(){(T[n].q=T[n].q||[]).push(arguments)});T[n].l=+new Date();if(T.attachEvent){T.attachEvent("onload",g)}else{T.addEventListener("load",g,false)}}(window,document,"script","tpwidget","//widget.seniverse.com/widget/chameleon.js"))</script>
                <script>tpwidget("init", {
                        "flavor": "slim",
                        "location": "WX4FBXXFKE4F",
                        "geolocation": "enabled",
                        "language": "zh-chs",
                        "unit": "c",
                        "theme": "chameleon",
                        "container": "tp-weather-widget",
                        "bubble": "disabled",
                        "alarmType": "badge",
                        "color": "#FFFFFF",
                        "uid": "U9EC08A15F",
                        "hash": "039da28f5581f4bcb5c799fb4cdfb673"
                    });
                    tpwidget("show");</script>
            </div>
            <!-- 顶部右侧菜单 -->
            <ul class="layui-nav top_menu">
                <li class="layui-nav-item" pc>
                    {{--openChat('https://chat8.live800.com/live800/chatClient/chatbox.jsp?companyID=903401&configID=152019&jid=2958998180&s=1&info={{ $_hasCode }}',900,600);--}}
                    {{--https://chat7.livechatvalue.com/chat/chatClient/chatbox.jsp?companyID=900939&amp;configID=63887&amp;jid=3324094179&amp;s=1--}}
                    <a href="javascript:;" onclick="openChat('https://ytpfx.livechatvalue.com/chat/chatClient/chatbox.jsp?companyID=1041184&configID=45162&jid=8125716336&s=1&info={{ $_hasCode }}',900,600);" class="layui-circle"><cite>在线客服</cite></a>
                </li>
                <li class="layui-nav-item" pc>
                    <span onclick="openOfficialWebsite()" style="cursor: pointer;" class="layui-circle">官网首页</span>
                </li>
                <li class="layui-nav-item" pc>
                    <a href="javascript:;" data-url="/user/deposit" data-key="deposit" class="layui-circle"><cite>账户存款</cite></a>
                </li>
                <li class="layui-nav-item" pc>
                    <a href="javascript:;" data-url="/user/withdraw" data-key="withdraw" class="layui-circle"><cite>账户取款</cite></a>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        {{--<img src="{{ URL::asset('img/user_header.jpg') }}?ver={{ resource_version_number() }}" class="layui-circle" width="35px" height="35px">--}}
                        @if(!empty($_user_info['img']) && $_user_info['img'][0]['img_header_path'] != null)
                            <img src="{{ URL::asset($_user_info['img'][0]['img_header_path']) }}?ver={{ resource_version_number() }}" class="layui-circle" width="35px" height="35px">
                        @else
                            <img src="{{ URL::asset('img/user_header.jpg') }}?ver={{ resource_version_number() }}" class="layui-circle" width="35px" height="35px">
                        @endif
                        {{--<img src="{{ URL::asset('js/plugins/layui/layadmin/modul/index/face.jpg') }}" class="layui-circle" width="35" height="35">--}}
                        <cite>{{ $_user_info['user_name'] }}({{ $_user_info['user_id'] }})</cite>
                        <span class="layui-nav-more"></span>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" data-url="/user/center"><i class="layui-icon">&#xe612;</i><cite>个人资料</cite></a></dd>
                        <dd><a href="javascript:;" data-url="/user/editpsw"><i class="layui-icon" data-icon="&#xe620;">&#xe620;</i><cite>修改密码</cite></a></dd>
                        {{--<dd><a href="javascript:;" class="changeSkin"><i class="layui-icon" data-icon="&#xe61b;">&#xe61b;</i><cite>更换皮肤</cite></a></dd>--}}
                        <dd><a href="{{ URL::asset('/user/loginOut') }}" class="signOut"><i class="layui-icon">&#xe64d;</i><cite>退出</cite></a></dd>
                    </dl>
                    
                </li>
            </ul>
        </div>
    </div>
    <!-- 左侧导航 -->
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                <li class="layui-nav-item layui-nav-itemed">
                    <a class="" href="javascript:;">我的账户</a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" data-url="/user/deposit" data-key="deposit"><cite>账户存款</cite></a></dd>
                        <dd><a href="javascript:;" data-url="/user/withdraw" data-key="withdraw"><cite>账户取款</cite></a></dd>
                        <dd><a href="javascript:;" data-url="/user/flow/main" data-key="flow"><cite>账户流水</cite></a></dd>
                    </dl>
                </li>
                @if($_role == 'Agents')
                    <li class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;">我的代理</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" data-url="/user/proxy/list" data-key="proxy_list"><cite>代理商列表</cite></a></dd>
                            <dd><a href="javascript:;" data-url="/user/proxy/confirm" data-key="proxy_confirm"><cite>待确认代理</cite></a></dd>
                        </dl>
                    </li>
                @endif
                @if($_role == 'Agents')
                    <li class="layui-nav-item"><a href="javascript:;" data-url="/user/position/summary" data-key="position_summary"><cite>仓位总结</cite></a></li>
                    <li class="layui-nav-item"><a href="javascript:;" data-url="/user/close/order" data-key="close_order"><cite>已平仓单</cite></a></li>
                    <li class="layui-nav-item"><a href="javascript:;" data-url="/user/open/order" data-key="open_order"><cite>未平仓单</cite></a></li>
                @else@if($_role == 'User')
                    <li class="layui-nav-item"><a href="javascript:;" data-url="/user/position/summary2" data-key="position_summary2"><cite>仓位总结</cite></a></li>
                    <li class="layui-nav-item"><a href="javascript:;" data-url="/user/close/order2" data-key="close_order2"><cite>已平仓单</cite></a></li>
                    <li class="layui-nav-item"><a href="javascript:;" data-url="/user/open/order2" data-key="open_order2"><cite>未平仓单</cite></a></li>
                @endif
                @if($_role == 'Agents')
                    <li class="layui-nav-item"><a href="javascript:;" data-url="/user/realtime/rebate" data-key="realtime_rebate"><cite>实时返佣</cite></a></li>
                    <li class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;">客户管理</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" data-url="/user/cust/list" data-key="cust_list"><cite>客户列表</cite></a></dd>
                            {{--<dd><a href="javascript:;" data-url="/user/change/list" data-key="change_list"><cite>变更列表</cite></a></dd>--}}
                        </dl>
                    </li>
                @endif
                {<li class="layui-nav-item"><a href="javascript:;" data-url="/user/news_list_browse" data-key="news_list_browse"><cite>最新公告</cite></a></li>
            </ul>
        </div>
    </div>
    <!-- 右侧内容 -->
    <div class="layui-body layui-form">
        <div class="layui-tab marg0" lay-filter="bodyTab" id="top_tabs_box">
            <ul class="layui-tab-title top_tab" id="top_tabs">
                <li class="layui-this" lay-id=""><i class="layui-icon icon-computer">&#xe68e;</i> <cite>账户首页</cite></li>
            </ul>
            <ul class="layui-nav closeBox">
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="layui-icon icon-caozuo">&#xe65f;</i> 页面操作</a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" class="refresh refreshThis"><i class="layui-icon">&#x1002;</i> 刷新当前</a></dd>
                        <dd><a href="javascript:;" class="closePageOther"><i class="layui-icon icon-prohibit">&#x1006;</i> 关闭其他</a></dd>
                        <dd><a href="javascript:;" class="closePageAll"><i class="layui-icon icon-guanbi">&#x1007;</i> 关闭全部</a></dd>
                    </dl>
                </li>
            </ul>
            <div class="layui-tab-content clildFrame">
                <div class="layui-tab-item layui-show">
                    <iframe src="/user/main/home" name="layui-layer-iframe-home" id="layui-layer-iframe-home"></iframe>
                </div>
            </div>
        </div>
    </div>
    <!-- 底部 -->
    <div class="layui-footer footer">
        <p>copyright @ {{ date ('Y') }} </p>
    </div>
</div>

<!-- 移动导航 -->
<div class="site-tree-mobile layui-hide"><i class="layui-icon">&#xe602;</i></div>
<div class="site-mobile-shade"></div>

<script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/layui/layui.js') }}?ver={{ resource_version_number() }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/modul/index/leftNav.js')}}?ver={{ resource_version_number() }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/modul/index/index.js') }}?ver={{ resource_version_number() }}"></script>
<script>
    function openOfficialWebsite() {
	    window.open("http://{{ Official_web_address() }}");
    }
</script>
</body>
</html>
