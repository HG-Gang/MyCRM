@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
    <form class="layui-form" action="" id="AgentsAddForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block">
                    <input type="text" name="username" id="username" autocomplete="off" placeholder="请输入用户名" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">性别</label>
                <div class="layui-input-block" style="width: 200px;">
                    <input type="radio" name="sex" value="男" title="男" checked="">
                    <input type="radio" name="sex" value="女" title="女">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">邀请码</label>
                <div class="layui-input-block">
                    <input type="text" name="userInviterId" id="userInviterId" autocomplete="off" placeholder="请输入邀请码" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">身份证ID</label>
                <div class="layui-input-block">
                    <input type="text" name="userIdcardNo" id="userIdcardNo" autocomplete="off" placeholder="请输入身份证ID" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-block">
                    <input type="text" name="userphoneNo" id="userphoneNo" autocomplete="off" placeholder="请输入手机号" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">邮箱</label>
                <div class="layui-input-block">
                    <input type="text" name="useremail" id="useremail" autocomplete="off" placeholder="请输入邮箱" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">用户组别</label>
                <div class="layui-input-inline" style="width: 200px; margin-right: 0px;">
                    <select name="usergrpId" id="usergrpId">
                        <option value="">请选用户组别</option>
                        @foreach($usergrpId as $val)
                            <option value="{{ $val['user_group_id'] }}">{{ $val['user_group_name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">代理级别</label>
                <div class="layui-input-inline" style="width: 200px; margin-right: 0px;">
                    <select name="useragtId" id="useragtId">
                        <option value="">请选择代理级别</option>
                        @foreach($userlvl as $val)
                            <option value="{{ $val['group_id'] }}">{{ $val['group_name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">返佣比例</label>
                <div class="layui-input-block">
                    <input type="text" name="userrebate" id="userrebate" autocomplete="off" placeholder="请输入返佣比例" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">账户模式</label>
                <div class="layui-input-inline" style="width: 200px; margin-right: 0px;">
                    <select name="usertype" id="usertype" lay-filter="trands_mode">
                        <option value="">请选账户模式</option>
                        <option value="0">返佣模式</option>
                        <option value="1">权益模式</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">结算周期</label>
                <div class="layui-input-inline" style="width: 200px; margin-right: 0px;" id="select_enabled">
                    <select name="usercycle" id="usercycle">
                        <option value="">请选择结算周期</option>
                        <option value="1">周结</option>
                        <option value="2">半月结</option>
                        <option value="3">月结</option>
                    </select>
                </div>
                <div class="layui-input-inline" style="width: 200px; display: none; margin-right: 0px;" id="select_disabled">
                    <select name="usercycle" id="usercycle" disabled>
                        <option value="">请选择结算周期</option>
                        <option value="1">周结</option>
                        <option value="2">半月结</option>
                        <option value="3">月结</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">权益</label>
                <div class="layui-input-block">
                    <input type="text" name="userrights" id="userrights" autocomplete="off" placeholder="请输入权益值" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">结算模式</label>
            <div class="layui-input-inline" style="width: 200px; margin-right: 13px;" id="rights_mode_QY">
                <select name="settlement_model" id="settlement_model">
                    <option value="">请选择结算模式</option>
                    <option value="1">线上结算</option>
                    <option value="2">线下结算</option>
                </select>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password" id="password" autocomplete="off" placeholder="请输入密码" class="layui-input" style="width: 200px;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">确认密码</label>
                <div class="layui-input-block">
                    <input type="password" name="againpassword" id="againpassword" autocomplete="off" placeholder="请输入确认密码" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" onclick="agentsAdd()">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        function agentsAdd() {
            console.log(getFormData("AgentsAddForm"));
            if (username() && userInviterId() && userIdcardNo() && userphoneNo()
                && useremail() && check_user_grp() && check_user_agtId()
                && check_user_rebate() && check_user_type() && check_settlement_model() && password() && againpassword()) {

                var index1 = openLoadShade();
                $.ajax({
                    url: route_prefix() + "/agents_save",
                    data: {
                        data:                   getFormData("AgentsAddForm"),
                        usergrpName:            $("#usergrpId option:selected").text(),
                        useragtName:            $("#useragtId option:selected").text(),
                        usercycle:              $("#usercycle option:selected").val(),
                        _token:					"{{ csrf_token() }}",
                    },
                    dateType: "JSON",
                    type: "POST",
                    async: false,
                    success: function(data) {
                        if (data.msg == "FAIL") {
                            closeLoadShade(index1);
                            if (data.err == "NonExist") {
                                errorTips("邀请码不存在!", "msg", data.col);
                            } else if (data.err == "ThanAndEqualInviter") {
                                errorTips("代理级别不能大于等于邀请人代理商级别!", "msg", data.col);
                            } else if (data.err == "Existidcard") {
                                errorTips("身份证已存在!", "msg", data.col);
                            } else if (data.err == "Existphone") {
                                errorTips("手机号已存在!", "msg", data.col);
                            } else if (data.err == "Existemail") {
                                errorTips("邮箱已存在!", "msg", data.col);
                            } else if (data.err == "Invalidgrp") {
                                errorTips("无效的用户组别!", "msg", data.col);
                            } else if (data.err == "ThanSysDefault") {
                                errorTips("返佣比例不能大于或小于系统预设值!", "msg", data.col);
                            } else if (data.err == "Nothanrebate") {
	                            errorTips("返佣比例不能大于上级返佣比例!", "msg", data.col);
                            } else if (data.err == "Nothanrights") {
	                            errorTips("权益比例不能大于上级权益比例!", "msg", data.col);
                            } else if (data.err == "Diffsettlmod") {
                                errorTips("请选择和邀请码账户的结算模式一致!", "msg", data.col);
                            } else if (data.err == "settlmodvalerr") {
                                errorTips("不合法的结算模式值,请重新刷新页面打开!", "msg", data.col);
                            } else/* if (data.err == "OPENFAIL") */{
                                layer.msg("开户失败", {
                                    //time: 20000, //20s后自动关闭
                                    btn: ['知道了'],
                                    yes: function (index, layero) {
                                        parent.layer.closeAll();
                                        window.location.href = "{{url(route_prefix()  .'/agents_add')}}";
                                    }
                                });
                            }
                        } else if (data.msg == "SUC") {
                            layer.msg("开户成功", {
                                time: 20000, //20s后自动关闭
                                btn: ['知道了'],
                                yes: function (index, layero) {
                                    parent.layer.closeAll();
                                    window.location.href = "{{url(route_prefix() . '/agents_add')}}";
                                },
                                end: function () {
                                    parent.layer.closeAll();
                                    window.location.href = "{{url(route_prefix() . '/agents_add')}}";
                                }
                            });
                        }
                    },
                    error:function(data) {
                        closeLoadShade(index1);
                        alert("系统错误");
                    }
                });
            }
        }
    </script>
@endsection