@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
    <form class="layui-form" action="" id="UserInfoForm" style="margin-top: 8px;">
        <div class="layui-form-item" enctype="multipart/form-data">
            <div class="layui-inline">
                @if ($type == 'phone')
                    <label class="layui-form-label">旧手机号</label>
                    <div class="layui-input-block">
                        <input type="text" name="oldphone" id="oldphone" value="{{ substr_replace(substr($_user_info['phone'], (stripos($_user_info['phone'], '-') + 1)), '*****', 3, -3) }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                    </div>
                @elseif ($type == 'email')
                    <label class="layui-form-label">旧邮箱</label>
                    <div class="layui-input-block">
                        <input type="text" name="oldemail" id="oldemail" value="{{ substr_replace($_user_info['email'], '*****', 3, (stripos($_user_info['email'], '@') - 3)) }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                    </div>
                @endif
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                @if ($type == 'phone')
                    <label class="layui-form-label">新手机号</label>
                    <div class="layui-input-block">
                        <input type="text" name="userphoneNo" id="userphoneNo" autocomplete="off" placeholder="请输入新手机号" class="layui-input" style="width: 270px;">
                    </div>
                @elseif ($type == 'email')
                    <label class="layui-form-label">新邮箱</label>
                    <div class="layui-input-block">
                        <input type="text" name="useremail" id="useremail" autocomplete="off" placeholder="请输入新邮箱" class="layui-input" style="width: 270px;">
                    </div>
                @endif
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">验证码</label>
            <div class="layui-input-inline">
                <input type="text" id="userverfcode" name="userverfcode" maxlength="6" placeholder="请输入验证码" autocomplete="off" class="layui-input" style="width: 163px;">
                <button type="button" id="getVerifyCode" onclick="funcGetVerifyCode()" class="layui-btn" style="margin-left: 163px;margin-top: -54px;">获取验证码</button>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password" id="password" autocomplete="off" placeholder="请输入密码" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" onclick="updatePhoneEmailInfo()">确定</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        function checkPhoneEmail() {
            var isTrue = true;
            if ("{{ $type }}" == 'email') {
                isTrue = useremail();
            } else if ("{{ $type }}" == 'phone') {
                isTrue = userphoneNo();
            }

            return isTrue;
        }

        function funcGetVerifyCode() {
            if (!$("#getVerifyCode").hasClass("layui-btn-disabled")) {
                if (checkPhoneEmail()) {
                    $.ajax({
                        url: '/user/center/updateVerifyInfo',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            userphoneNo:        $.trim($("#userphoneNo").val()),
                            useremail:          $.trim($("#useremail").val()),
                            type:               "{{ $type }}",
                            _token:             "{{ csrf_token() }}",
                        },
                        error: function (msg) {
                            layer.msg("网络故障,请稍后再操作", {icon: 5, shift: 6});
                        },
                        success: function (msg) {
                            console.log(msg);
                            if (msg.msg == 'FAIL') {
                                if (msg._tel == 'userphoneNo') {
                                    errorTips('手机号已存在!', 'msg', 'userphoneNo');
                                } else if (msg._eml == 'useremail') {
                                    errorTips('邮箱已存在!', 'msg', 'useremail');
                                }
                            } else if (msg.msg == 'SUC') {
                                /*验证通过，开始发送验证码*/
                                updateverifyPassSendCode();
                            }
                        }
                    });
                }
            }
        }
        
        function updateverifyPassSendCode() {
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
                    url: '/user/center/updVerifyPassSendCode',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        userphoneNo:        $.trim($("#userphoneNo").val()),
                        useremail:          $.trim($("#useremail").val()),
                        type:               "{{ $type }}",
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
        
        function updatePhoneEmailInfo() {
            if (checkPhoneEmail() && userverfcode() && password()) {
                var index = openLoadShade();
                $.ajax({
                    url: '/user/center/updatePhoneEmailInfo',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        userphoneNo:        $.trim($("#userphoneNo").val()),
                        useremail:          $.trim($("#useremail").val()),
                        updVerifyCode:      $.trim($("#userverfcode").val()),
                        password:           $.trim($("#password").val()),
                        type:               "{{ $type }}",
                        _token:             "{{ csrf_token() }}",
                    },
                    error: function (msg) {
                        closeLoadShade(index);
                        layer.msg("网络故障,请稍后操作!", {icon: 5, shift: 6});
                    },
                    success: function (msg) {
                        closeLoadShade(index);
                        if (msg.msg == "SUC") {
                            layer.msg("更改成功", {
                                time: 20000, //20s后自动关闭
                                btn: ['知道了'],
                                yes: function (index, layero) {
                                    parent.layer.closeAll();
                                    top.location = '/user/index';
                                }
                            });
                        } else {
                            if (msg.msg == "FAIL") {
                                if (msg.err == "codeErr") {
                                    errorTips("验证码错误!", "msg", msg.col);
                                } else if (msg.err == "phoneErr") {
                                    errorTips("接收验证码手机和新手机号不一致!", "msg", msg.col);
                                } else if (msg.err == "emailErr") {
                                    errorTips("接收验证码邮箱和新邮箱不一致!", "msg", msg.col);
                                } else if (msg.err == "NETWORKFAIL") {
                                    layer.msg("网络故障,请稍后重试!", {icon: 5, shift: 6});
                                } else if (msg.err == "pswErr") {
                                    errorTips("密码错误!", "msg", msg.col);
                                } else if (msg.err == "UPDATEFAIL") {
                                    layer.msg("更改失败,请稍后重试!", {icon: 5, shift: 6});
                                }
                            }
                        }
                    }
                });
            }
        }
    </script>
@endsection