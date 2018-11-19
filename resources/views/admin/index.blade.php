<!DOCTYPE HTML>
<html>
    <head>
        @include('admin.include.head')
        <!--[if IE 6]>
        <script type="text/javascript" src="lib/DD_belatedPNG_0.0.8a-min.js" ></script>
        <script>DD_belatedPNG.fix('*');</script>
        <![endif]-->
        <title>帕达控股后台管理</title>
        <meta name="keywords" content="">
        <meta name="description" content="">
    </head>
    <style>
        .Hui-aside .menu_dropdown li a:hover {
            color: #148cf1;
            background-color:#656d73;
            opacity: .5;
        }
        .Hui-aside .menu_dropdown dt{
            border-bottom: 1px solid #b6b7b8;
        }
    </style>
    <body>
        <header class="navbar-wrapper">
            <div class="navbar navbar-fixed-top">
                <div class="container-fluid cl"> <a class="logo navbar-logo f-l mr-10 hidden-xs" >帕达控股后台管理系统</a> <a class="logo navbar-logo-m f-l mr-10 visible-xs" href="">H-ui</a>
                    <span class="logo navbar-slogan f-l mr-10 hidden-xs"></span>
                    <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
                    <!--                    <nav class="nav navbar-nav">
                                            <ul class="cl">
                                                <li class="dropDown dropDown_hover"><a href="javascript:;" class="dropDown_A"><i class="Hui-iconfont">&#xe600;</i> 新增 <i class="Hui-iconfont">&#xe6d5;</i></a>
                                                    <ul class="dropDown-menu menu radius box-shadow">
                                                        <li><a href="javascript:;" onclick="article_add('添加资讯', 'article-add.html')"><i class="Hui-iconfont">&#xe616;</i> 资讯</a></li>
                                                        <li><a href="javascript:;" onclick="picture_add('添加资讯', 'picture-add.html')"><i class="Hui-iconfont">&#xe613;</i> 图片</a></li>
                                                        <li><a href="javascript:;" onclick="product_add('添加资讯', 'product-add.html')"><i class="Hui-iconfont">&#xe620;</i> 产品</a></li>
                                                        <li><a href="javascript:;" onclick="member_add('添加用户', 'member-add.html', '', '510')"><i class="Hui-iconfont">&#xe60d;</i> 用户</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </nav>-->
                    <nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
                        <ul class="cl">
                            <li>{{RoleName($admin->role_id)}}</li>
                            <li class="dropDown dropDown_hover">
                                <a href="#" class="dropDown_A">{{$admin->username}} <i class="Hui-iconfont">&#xe6d5;</i></a>
                                <ul class="dropDown-menu menu radius box-shadow">
                                    <!--<li><a href="javascript:;" onClick="myselfinfo()">个人信息</a></li>-->
                                    <li><a title="个人资料" href="javascript:;" onclick="userinfo_edit('个人资料', '{{url(route_prefix() . "/userinfo")}}', '800', '500')"  >个人资料</a></li>
                                    <li><a title="修改密码" href="javascript:;" onclick="userinfo_edit1('修改密码', '{{url(route_prefix() ."/userpwd")}}', '800', '500')"  >修改密码</a></li>
                                    <li><a href="{{url(route_prefix() . '/logout')}}">退出</a></li>
                                </ul>
                            </li>
                            <!--<li id="Hui-msg"> <a href="#" title="消息"><span class="badge badge-danger">1</span><i class="Hui-iconfont" style="font-size:18px">&#xe68a;</i></a> </li>-->
                            <li id="Hui-skin" class="dropDown right dropDown_hover"> <a href="javascript:;" class="dropDown_A" title="换肤"><i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i></a>
                                <ul class="dropDown-menu menu radius box-shadow">
                                    <li><a href="javascript:;" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
                                    <li><a href="javascript:;" data-val="blue" title="蓝色">蓝色</a></li>
                                    <li><a href="javascript:;" data-val="green" title="绿色">绿色</a></li>
                                    <li><a href="javascript:;" data-val="red" title="红色">红色</a></li>
                                    <li><a href="javascript:;" data-val="yellow" title="黄色">黄色</a></li>
                                    <li><a href="javascript:;" data-val="orange" title="橙色">橙色</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>
        <aside class="Hui-aside" style="background:#393D49 ">
            <div class="menu_dropdown bk_2">
                @if($p==1)
                @foreach($admin_menu as $v)
                <dl id="menu-product">
                    <dt style="color: #fff"><i class="Hui-iconfont" style="color: #fff">{{$v['icon']}}</i> {{$v['label']}}
                        @if($v['url'] == '#')
                        <i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                        @endif
                    </dt>
                    <dd>
                        @if(!empty($v['submenu']))
                        <ul>
                            @foreach($v['submenu'] as $vv)
                            <li class="bank"><a data-href="{{url($vv['url'])}}" data-title="{{$vv['label']}}" href="javascript:void(0)" style="color: #fff" class="bank">{{$vv['label']}}</a></li>
                            @endforeach
                        </ul>
                        @endif
                    </dd>
                </dl>
                @endforeach
                @else
                @foreach($admin_menu as $v)
                @if( in_array($v['menu_tag'],$permissions) )
                <dl id="menu-product">
                    <dt style="color: #fff"><i class="Hui-iconfont" style="color: #fff">{{$v['icon']}}</i> {{$v['label']}}
                        @if($v['url'] == '#')
                        <i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                        @endif
                    </dt>
                    <dd>
                        @if(!empty($v['submenu']))
                        <ul>
                            @foreach($v['submenu'] as $vv)
                            @if(in_array($vv['menu_tag'],$permissions))
                            <li  class="bank"><a data-href="{{url($vv['url'])}}" data-title="{{$vv['label']}}" href="javascript:void(0)" style="color: #fff">{{$vv['label']}}</a></li>
                            @endif
                            @endforeach
                        </ul>
                        @endif
                    </dd>
                </dl>
                @endif
                @endforeach
                @endif
            </div>
        </aside>
        <div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
        <section class="Hui-article-box">
            <div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
                <div class="Hui-tabNav-wp">
                    <ul id="min_title_list" class="acrossTab cl">
                        <li class="active">
                            <span title="我的桌面" data-href="welcome.html">我的桌面</span>
                            <em></em></li>
                    </ul>
                </div>
                <div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
            </div>
            <div id="iframe_box" class="Hui-article">
                <div class="show_iframe">
                    <div style="display:none" class="loading"></div>
                    <iframe scrolling="yes" frameborder="0" src="{{url(route_prefix() . '/welcome')}}"></iframe>
                </div>
            </div>
        </section>

        <div class="contextMenu" id="Huiadminmenu">
            <ul>
                <li id="closethis">关闭当前 </li>
                <li id="closeall">关闭全部 </li>
            </ul>
        </div>
        <!-- 底部 -->

        <!--_footer 作为公共模版分离出去-->
        @include('admin.include.footer')

        <!--请在下方写此页面业务相关的脚本-->
        <script type="text/javascript" src="{{asset('/admin/lib/jquery.contextmenu/jquery.contextmenu.r2.js')}}"></script>
        <script type="text/javascript">

            /*个人信息*/
            function myselfinfo() {
            alert();
            var html = "<div style='padding-left:20px'><p>电话号码：{{$admin->mobile}}</p><p>邮箱：{{$admin->email}}</p></div>";
            layer.open({
            type: 1,
                    area: ['400px', '300px'],
                    fix: false, //不固定
                    maxmin: true,
                    shade: 0.4,
                    title: '查看信息',
                    content: html
            });
            }
            /*管理员-编辑*/
            function userinfo_edit(title, url, w, h) {
            layer_show(title, url, w, h);
            }
            function userinfo_edit1(title, url, w, h) {
            layer_show(title, url, w, h);
            }
            /*资讯-添加*/
            function article_add(title, url) {
            var index = layer.open({
            type: 2,
                    title: title,
                    content: url
            });
            layer.full(index);
            }
            /*图片-添加*/
            function picture_add(title, url) {
            var index = layer.open({
            type: 2,
                    title: title,
                    content: url
            });
            layer.full(index);
            }
            /*产品-添加*/
            function product_add(title, url) {
            var index = layer.open({
            type: 2,
                    title: title,
                    content: url
            });
            layer.full(index);
            }
            /*用户-添加*/
            function member_add(title, url, w, h) {
            layer_show(title, url, w, h);
            }

        </script>
    </body>
</html>
