<!DOCTYPE HTML>
<html>
    <head>
        @include('admin.include.head')
        <!--[if IE 6]>
        <script type="text/javascript" src="lib/DD_belatedPNG_0.0.8a-min.js" ></script>
        <script>DD_belatedPNG.fix('*');</script>
        <![endif]-->
        <title>编辑代理商</title>
        <meta name="keywords" content="">
        <meta name="description" content="">
    </head>
    <style>
        .form-child-div{
            width: 50%;
            float: left;
            margin-bottom: 16px;
        }
        .find-font{
            width: 50px;
        }
        .form-child-div>input{
            width:71%;
        } 
        .form-child-div>select{
            width:71%;
        }
        .form-child-div>input-text1{
            width: 30px;
        } 
        .form-child-div>.check1{
            width: 20px;
        }
        #cycle[disabled="disabled"] {
            background: rgb(235, 235, 228);
        }
        #mt4_grp[disabled] {
            background: rgb(235, 235, 228);
        }
        #group_id[disabled] {
            background: rgb(235, 235, 228);
        }
     
        
    </style>
    <body>
        <article class="page-container">
            @if($state==1)
            <!--超级管理员-->
            @include('admin.agent.admin')
            @elseif($state==2)
            <!--客户-->
            @include('admin.agent.ust')
            @elseif($state==3)
            <!--财务-->
            @include('admin.agent.finance')
            @elseif($state==0)
            <div style="margin: auto auto;font-size: 30px;">你的没有相应权限</div>
            @endif
        </article>

        <!--_footer 作为公共模版分离出去--> 
        @include('admin.include.footer')
        <!--请在下方写此页面业务相关的脚本-->
        <script type="text/javascript" src="{{asset('/admin/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script> 
        <script type="text/javascript" src="{{asset('/admin/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script> 
        <script type="text/javascript" src="{{asset('/admin/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script> 
        <script type="text/javascript">
$(function () {
    $("#trans_mode").change(function () {
        var obj = $("#trans_mode option:selected");
        if (obj.val() == 0) {
            $("#rights").val('');
            $("#rights").attr("disabled", "disabled");
            $('#cycle').attr("disabled", "disabled");
            $('#cycle').val('请选择结算周期');

        } else {
            $("#rights").removeAttr("disabled");
            $("#cycle").removeAttr("disabled");
            $("#rights").val('{{$user->rights}}');
            $('#cycle').val('{{$user->cycle}}');
        }


    })












    $('.skin-minimal input').iCheck({
        checkboxClass: 'icheckbox-blue',
        radioClass: 'iradio-blue',
        increaseArea: '20%'
    });

    $("#form-admin-add").validate({
        rules: {
//            user_id: {
//                required: true,
//                minlength: 4,
//                maxlength: 16
//            },
//            password: {
//                required: true,
//            },
//            password2: {
//                required: true,
//                equalTo: "#password"
//            },
//            sex: {
//                required: true,
//            },
//            phone: {
//                required: true,
//                isPhone: true,
//            },
//            email: {
//                required: true,
//                email: true,
//            },
//            adminRole: {
//                required: true,
//            },
        },
        onkeyup: false,
        focusCleanup: true,
        success: "valid",
        submitHandler: function (form) {
            $(form).ajaxSubmit({
                type: 'post',
                url: "{{url(route_prefix() . '/agent/update')}}",
                dataType: 'json',
                success: function (data) {
                    if (data.statue == 1) {
                        layer.msg('添加成功!', {icon: 1, time: 2000});
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
        parent.window.location.href = "{{url(route_prefix() . '/Administrators')}}";
        parent.layer.close(index);
    }
});

        </script> 
        <!--/请在上方写此页面业务相关的脚本-->
    </body>
</html>