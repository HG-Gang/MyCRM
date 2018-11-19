<!DOCTYPE HTML>
<html>
    <head>
        @include('admin.include.head')
        <!--[if IE 6]>
        <script type="text/javascript" src="lib/DD_belatedPNG_0.0.8a-min.js" ></script>
        <script>DD_belatedPNG.fix('*');</script>
        <![endif]-->
        <title>添加管理员 - 管理员管理 </title>
        <meta name="keywords" content="">
        <meta name="description" content="">
    </head>
    <body>
        <article class="page-container">
            <form class="form form-horizontal" id="form-admin-add">
                <div class="row cl">
                    {{ csrf_field() }}
                    <input type="hidden" name="role_id" value="{{$info->role_id}}">
                    <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>角色：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" class="input-text" value="{{$info->username}}" placeholder="角色" id="adminName" name="username">
                    </div>
                </div>

                <!--                <div class="row cl">
                                    <label class="form-label col-xs-4 col-sm-3">权限：</label>
                                    <div class="formControls col-xs-8 col-sm-9">
                                        @foreach($uri_list as $v)
                                        <div style="margin-bottom:10px;">
                                            <input type="checkbox" name="acl[]" value="{{$v['menu_tag']}}" <?php if (in_array($v['menu_tag'], $permission)) echo 'checked'; ?> >{{$v['menu_name']}}<br>
                                            @if(!empty($v['sub_menu']))
                                            @foreach($v['sub_menu'] as $vv)
                                            {{str_repeat('&nbsp;',4)}}<input type="checkbox" name="acl[]" value="{{$vv['menu_tag']}}" <?php if (in_array($vv['menu_tag'], $permission)) echo 'checked'; ?>  >{{$vv['menu_name']}}<br>
                                            @if(!empty($vv['uri']))
                                            @foreach($vv['uri'] as $k=>$vvv)
                                            {{str_repeat('&nbsp;',8)}}<input type="checkbox" name="acl[]" value="{{$k}}" <?php if (in_array($k, $permission)) echo 'checked'; ?> >{{$vvv}}
                                            @endforeach
                                            <br>
                                            @endif
                                            @endforeach
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>-->

                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">权限选择：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        @foreach($uri_list as $v)
                        <dl class="permission-list">
                            <dt>
                                <label>
                                    <input type="checkbox" value="{{$v['menu_tag']}}" name="acl[]" id="user-Character-0" @if(in_array($v['menu_tag'], $permission)) checked @endif >
                                           {{$v['menu_name']}}</label>
                            </dt>
                            @if(!empty($v['sub_menu']))
                            @foreach($v['sub_menu'] as $vv)
                            <dd>
                                <dl class="cl permission-list2">
                                    <dt>
                                        <label class="">
                                            <input type="checkbox" value="{{$vv['menu_tag']}}" name="acl[]" id="user-Character-0-0"  @if(in_array($vv['menu_tag'], $permission)) checked @endif>
                                                   {{$vv['menu_name']}}</label>
                                    </dt>
                                    <dd>
                                        @if(!empty($vv['uri']))
                                        @foreach($vv['uri'] as $k=>$vvv)
                                        <label class="">
                                            <input type="checkbox" value="{{$k}}" name="acl[]" id="user-Character-0-0-0"  @if(in_array($k, $permission)) checked @endif>
                                                   {{$vvv}}</label>
                                        @endforeach
                                        @endif
                                    </dd>
                                </dl>   
                            </dd>
                            @endforeach
                            @endif  
                        </dl>
                        @endforeach
                    </div>
                </div>



                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">描述：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <textarea name="desc" cols="" rows="" class="textarea"  placeholder="描述......" dragonfly="true" onKeyUp="$.Huitextarealength(this, 100)">{{$info->desc}}</textarea>
                        <p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>
                    </div>
                </div>
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
        <script type="text/javascript" src="{{asset('/admin/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script> 
        <script type="text/javascript" src="{{asset('/admin/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script> 
        <script type="text/javascript" src="{{asset('/admin/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script> 
        <script type="text/javascript">

                            $(function () {
                                $(".permission-list dt input:checkbox").click(function () {
                                    $(this).closest("dl").find("dd input:checkbox").prop("checked", $(this).prop("checked"));
                                });
                                $(".permission-list2 dd input:checkbox").click(function () {
                                    var l = $(this).parent().parent().find("input:checked").length;
                                    var l2 = $(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
                                    if ($(this).prop("checked")) {
                                        $(this).closest("dl").find("dt input:checkbox").prop("checked", true);
                                        $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked", true);
                                    } else {
                                        if (l == 0) {
                                            $(this).closest("dl").find("dt input:checkbox").prop("checked", false);
                                        }
                                        if (l2 == 0) {
                                            $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked", false);
                                        }
                                    }
                                });



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
                                        password: {
                                            required: true,
                                        },
                                        password2: {
                                            required: true,
                                            equalTo: "#password"
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
                                            url: "{{url(route_prefix() . '/role/editsave')}}",
                                            dataType: 'json',
                                            success: function (data) {
                                                if (data.state == 1) {
                                                    layer.msg(data.msg, {icon: 1, time: 2000});
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
                                    parent.window.location.href = "{{$url}}";
                                    parent.layer.close(index);
                                }
                            });

        </script> 
        <!--/请在上方写此页面业务相关的脚本-->
    </body>
</html>