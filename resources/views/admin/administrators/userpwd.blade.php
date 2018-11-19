<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>修改密码</title>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="format-detection" content="telephone=no">
        <link rel="stylesheet" href="{{asset('admin/as/layui/css/layui.css')}}" media="all" />
        <link rel="stylesheet" href="{{asset('admin/as/css/user.css')}}" media="all" />
    </head>
    <body class="childrenBody">
        <form class="layui-form">
            <div class="user_left">
                <div class="layui-form-item">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-block">
                        <input type="text" value="{{$admin->username}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>           
                <div class="layui-form-item">
                    <label class="layui-form-label">旧密码</label>
                    <div class="layui-input-block">
                        <input type="password" value="" placeholder="请输入旧密码" lay-verify="required|phone" class="layui-input" name="pwd" id="pwd">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">新密码</label>
                    <div class="layui-input-block">
                        <input type="password" value="" placeholder="请输入新密码" lay-verify="required|email" class="layui-input" name="npwd" id="npwd">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">确定新密码</label>
                    <div class="layui-input-block">
                        <input type="password" value="" placeholder="请输入确定新密码" lay-verify="required|email" class="layui-input" name="rpwd" id="rpwd">
                    </div>
                </div>
            </div>

            <div class="layui-form-item" style="margin-left: 5%;">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit="" lay-filter="changeUser" type="button" id="but">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>

        <script type="text/javascript" src="{{asset('admin/as/js/jquery-2.1.1.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/layer/layer.js')}}"></script>
      <!--<script type="text/javascript" src="{{asset('admin/as/js/user.js')}}"></script>-->
        <script>
$(function () {
    $('#but').click(function () {
        var pwd = $('#pwd').val();
        var npwd = $('#npwd').val();
        var rpwd = $('#rpwd').val();
        if (pwd == "") {
            layer.msg('请输入旧密码', {icon: 2, time: 2000});
            return false;
        }
        if (npwd == "") {
            layer.msg('请设置新密码', {icon: 2, time: 2000});
            return false;
        }
        if (npwd != rpwd) {
            layer.msg('两次密码不一致', {icon: 2, time: 2000});
            return false;
        }
        $.ajax({
            url: "{{url(route_prefix() . '/userpwd/save')}}",
            type: 'post',
            data: {
                'pwd': pwd,
                'npwd': npwd,
                '_token': '{{csrf_token()}}'
            },
            dataType: "json",
            success: function (d) {
                if (d.state == 1) {
                    layer.msg(d.msg, {icon: 1, time: 2000});
                } else {
                    layer.msg(d.msg, {icon: 2, time: 2000});
                }
            }




        });




    });
})




        </script>




    </body>
</html>