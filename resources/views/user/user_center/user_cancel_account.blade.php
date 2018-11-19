@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
    <div>
        <ol style="background: #fef7e4; text-indent: 1em;font-size: 13px;">
            <li>1. 只有账户没有持仓单且账户余额大于等于零, 才能提交申请.</li>
            <li>2. 如果出金申请有正在处理的订单则不能成功提交销户申请.</li>
            <li>3. 当申请成功后, 账户将不能进行任何交易及出金操作.</li>
            <li>4. 当客服审核后, 将会以发送短信方式通知您处理结果.</li>
        </ol>
    </div>
    <form class="layui-form" action="" id="UserInfoForm" style="margin-top: 8px;">
        <div class="layui-form-item" enctype="multipart/form-data">
            <div class="layui-inline">
                <label class="layui-form-label">账号ID</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" id="userId" value="{{ $_user_info['user_id'] }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">身份证号</label>
                <div class="layui-input-block">
                    <input type="text" name="userIdcardNo" id="userIdcardNo" autocomplete="off" placeholder="{{ substr_replace($_user_info['IDcard_no'], '************', 3, -4) }}" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-block">
                    <input type="text" name="userphoneNo" id="userphoneNo" autocomplete="off" placeholder="{{ substr_replace((substr($_user_info['phone'], (stripos($_user_info['phone'], '-') + 1))), '*****', 3, -4) }}" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">邮箱</label>
                <div class="layui-input-block">
                    <input type="text" name="useremail" id="useremail" autocomplete="off" placeholder="{{ substr_replace($_user_info['email'], '*****', 3, (stripos($_user_info['email'], '@') - 3)) }}" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">手机验证码</label>
            <div class="layui-input-inline">
                <input type="text" id="userverfcode" name="userverfcode" maxlength="6" placeholder="请输入验证码" autocomplete="off" class="layui-input" style="width: 163px;">
                <button type="button" id="getVerifyCode" onclick="funcGetCancelVerifyCode()" class="layui-btn" style="margin-left: 163px;margin-top: -54px;">获取验证码</button>
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
                <button type="button" class="layui-btn" onclick="submintCancelApply()">提交申请</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        function funcGetCancelVerifyCode() {
            if (!$("#getVerifyCode").hasClass("layui-btn-disabled")) {
                if (userIdcardNo() && userphoneNo() && useremail()) {
                    $.ajax({
                        url: '/user/center/cancelVerifyInfo',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            userIdcardNo:       $.trim($("#userIdcardNo").val()),
                            userphoneNo:        $.trim($("#userphoneNo").val()),
                            useremail:          $.trim($("#useremail").val()),
                            _token:             "{{ csrf_token() }}",
                        },
                        error: function (msg) {
                            layer.msg("网络故障,请稍后再操作", {icon: 5, shift: 6});
                        },
                        success: function (msg) {
                            console.log(msg);
                            if (msg.msg == 'FAIL') {
                                if (msg.err == 'phoneErr') {
                                    errorTips('手机号有误!', 'msg', msg.col);
                                } else if (msg.err == 'emailErr') {
                                    errorTips('邮箱有误!', 'msg', msg.col);
                                } else if (msg.err == 'IDcardnoErr') {
                                    errorTips('身份证号有误!', 'msg', msg.col);
                                } else if (msg.err == 'verifyCodeErr') {
                                    errorTips('验证码有误!', 'msg', msg.col);
                                }
                            } else if (msg.msg == 'SUC') {
                                /*验证通过，开始发送验证码*/
                                cancelVerifyPassSendCode();
                            }
                        }
                    });
                }
            }
        }

        function cancelVerifyPassSendCode() {
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
                    url: '/user/center/cancelVerifyPassSendCode',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        userIdcardNo:       $.trim($("#userIdcardNo").val()),
                        userphoneNo:        $.trim($("#userphoneNo").val()),
                        useremail:          $.trim($("#useremail").val()),
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

        function submintCancelApply() {
            if (userIdcardNo() && userphoneNo() && useremail() && password() && userverfcode()) {
                var index = openLoadShade();
                $.ajax({
                    url: '/user/center/ajaxCancelAccount',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        userIdcardNo:       $.trim($("#userIdcardNo").val()),
                        userphoneNo:        $.trim($("#userphoneNo").val()),
                        useremail:          $.trim($("#useremail").val()),
                        password:           $.trim($("#password").val()),
                        userverfcode:       $.trim($("#userverfcode").val()),
                        _token:             "{{ csrf_token() }}",
                    },
                    error: function (msg) {
                        closeLoadShade(index);
                        layer.msg("网络故障,请稍后操作!", {icon: 5, shift: 6});
                    },
                    success: function (msg) {
                        closeLoadShade(index);
                        if (msg.msg == "SUC") {
                            layer.msg("申请成功", {
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
                                } else if (msg.err == "passwordErr") {
                                    errorTips("密码错误!", "msg", msg.col);
                                } else if (msg.err == "ERRBALANCE") {
                                    layer.msg("余额小于0,不满足申请条件!", {icon: 5, shift: 6});
                                } else if (msg.err == "ERRVOL") {
                                    layer.msg("账户有持仓单,不满足申请条件!", {icon: 5, shift: 6});
                                } else if (msg.err == "existSubUser") {
                                    layer.msg("当前账户存在直属客户,无法申请注销,详情请咨询客服!", {icon: 5, shift: 6});
                                } else if (msg.err == "UnfinishedOrder") {
                                    layer.msg("有出金申请未处理的订单,不满足申请条件!", {icon: 5, shift: 6});
                                } else if (msg.err == "cancelApplyErr") {
                                    layer.msg("销户申请失败,请稍后重试!", {icon: 5, shift: 6});
                                } else if (msg.err == "MT4SYNCUPDATAFAIL") {
                                    layer.msg("与Mt4握手失败,请稍后重试!", {icon: 5, shift: 6});
                                } else if (msg.err == "NETWORKFAIL") {
                                    layer.msg("网络故障,请稍后重试!", {icon: 5, shift: 6});
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