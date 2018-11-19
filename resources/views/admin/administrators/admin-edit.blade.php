<!DOCTYPE HTML>
<html>
    <head>
        @include('admin.include.head')
        <!--[if IE 6]>
        <script type="text/javascript" src="lib/DD_belatedPNG_0.0.8a-min.js" ></script>
        <script>DD_belatedPNG.fix('*');</script>
        <![endif]-->
        <title>添加管理员 - 管理员管理 </title>
        <meta name="keywords" content="H-ui.admin v3.1,H-ui网站后台模版,后台模版下载,后台管理系统模版,HTML后台模版下载">
        <meta name="description" content="H-ui.admin v3.1，是一款由国人开发的轻量级扁平化网站后台模板，完全免费开源的网站后台管理系统模版，适合中小型CMS后台系统。">
    </head>
    <body>
        <article class="page-container">
            <form class="form form-horizontal" id="form-admin-add">
                <div class="row cl">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{$info->id}}">
                    <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>管理员：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" class="input-text" value="{{$info->username}}" placeholder="账号" id="adminName" name="username">
                    </div>
                </div>
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>原始密码：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="password" class="input-text" autocomplete="off" value="" placeholder="密码" id="password" name="password">
                    </div>
                </div>
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>新密码：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="password" class="input-text" autocomplete="off"  placeholder="确认新密码" id="password2" name="password2">
                    </div>
                </div>
                <!--	<div class="row cl">
                                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>性别：</label>
                                <div class="formControls col-xs-8 col-sm-9 skin-minimal">
                                        <div class="radio-box">
                                                <input name="sex" type="radio" id="sex-1" checked>
                                                <label for="sex-1">男</label>
                                        </div>
                                        <div class="radio-box">
                                                <input type="radio" id="sex-2" name="sex">
                                                <label for="sex-2">女</label>
                                        </div>
                                </div>
                        </div>-->
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>手机：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" class="input-text" value="{{$info->mobile}}" placeholder="" id="phone" name="mobile">
                    </div>
                </div>
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>邮箱：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" class="input-text" placeholder="@" name="email" id="email" value="{{$info->email}}">
                    </div>
                </div>
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">角色：</label>
                    <div class="formControls col-xs-8 col-sm-9"> <span class="select-box" style="width:150px;">
                            <select class="select" name="role_id" size="1">
                                @foreach($role as $v)
                                <option value="{{$v->role_id}}" @if($info->role_id==$v->role_id) selected @endif>{{$v->username}}</option>
                                @endforeach
                            </select>
                        </span> </div>
                </div>
                <!--	<div class="row cl">
                                <label class="form-label col-xs-4 col-sm-3">备注：</label>
                                <div class="formControls col-xs-8 col-sm-9">
                                        <textarea name="" cols="" rows="" class="textarea"  placeholder="说点什么...100个字符以内" dragonfly="true" onKeyUp="$.Huitextarealength(this,100)"></textarea>
                                        <p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>
                                </div>
                        </div>-->
                <div class="row cl">
                    <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                        <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
                    </div>
                </div>
            </form>
        </article>

        <!--_footer 作为公共模版分离出去-->
        @include('admin.include.footer')
        <!--请在下方写此页面业务相关的脚本-->
        <script type="text/javascript" src="{{asset('admin/lib/jquery.validation/1.14.0/jquery.validate.js')}}?ver={{ resource_version_number() }}"></script>
        <script type="text/javascript" src="{{asset('admin/lib/jquery.validation/1.14.0/validate-methods.js')}}?ver={{ resource_version_number() }}"></script>
        <script type="text/javascript" src="{{asset('admin/lib/jquery.validation/1.14.0/messages_zh.js')}}?ver={{ resource_version_number() }}"></script>
        <script type="text/javascript">
$(function () {
    $('.skin-minimal input').iCheck({
        checkboxClass: 'icheckbox-blue',
        radioClass: 'iradio-blue',
        increaseArea: '20%'
    });

    $("#form-admin-add").validate({
        rules: {
            adminName: {
                required: true,
                minlength: 4,
                maxlength: 16
            },
            sex: {
                required: true,
            },
            phone: {
                required: true,
                isPhone: true,
            },
            email: {
                required: true,
                email: true,
            },
            adminRole: {
                required: true,
            },
        },
        onkeyup: false,
        focusCleanup: true,
        success: "valid",
        submitHandler: function (form) {
            $(form).ajaxSubmit({
                type: 'post',
                url: "{{url(route_prefix() . '/Administrators/editsave')}}",
                dataType: 'json',
                success: function (data) {
                    if (data.statue == 1) {
                        layer.msg('编辑成功!', {icon: 1, time: 2000});
                        setTimeout(test, 2000);
                    } else {
                        layer.msg(data.msg, {icon: 2, time: 2000});
                    }
                }
            });
        }
    });
    function test() {
    var index = parent.layer.getFrameIndex(window.name);
    parent.$('.btn-refresh').click();
    top.window.location.href = "{{$url}}";
    parent.layer.close(index);
}
});

        </script>
        <!--/请在上方写此页面业务相关的脚本-->
    </body>
</html>