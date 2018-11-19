@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
    <form class="layui-form" action="" id="proxyListForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">旧密码</label>
                <div class="layui-input-block">
                    <input type="password" name="olduserpsw" id="olduserpsw" autocomplete="off" placeholder="请输入旧密码" class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">新密码</label>
                <div class="layui-input-block">
                    <input type="password" name="newuserpsw" id="newuserpsw" autocomplete="off" placeholder="请输入新密码"class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">确认密码</label>
                <div class="layui-input-block">
                    <input type="password" name="confirmuserpsw" id="confirmuserpsw" autocomplete="off" placeholder="请输入确认密码"class="layui-input" style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" onclick="userpsw_save()">确认</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-resources')
    <script type="text/javascript">
        function check_passsword() {
            var old_password = $("#olduserpsw").val();
            var new_password = $("#newuserpsw").val();
            var password_again = $("#confirmuserpsw").val();
            var pasReg = /^[a-zA-Z][\w\W]*\d$/;

            if(old_password == "") {
                errorTips("请输入旧的密码!", "msg", "olduserpsw");
            } else if(new_password == "") {
                errorTips("请输入新的密码!", "msg", "newuserpsw");
            } else if (!pasReg.test(new_password) && new_password != "") {
                errorTips("密码以字母开首,且以数字结尾!", "msg", "newuserpsw");
            }else if ($.trim(new_password).length < 6) {
                errorTips("请输入6位以上密码!", "msg", "newuserpsw");
            } else if(password_again == "") {
                errorTips("请输入确认密码!", "msg", "confirmuserpsw");
            } else if(password_again != new_password && new_password != "") {
                errorTips("两次密码不一样!", "msg", "confirmuserpsw");
            } else if(password_again == old_password) {
                errorTips("新密码不能和就密码一样!", "msg", "newuserpsw");
            } else {
                return true;
            }
        }

        function userpsw_save() {
            if (check_passsword()) {
                var old_password = $("#olduserpsw").val();
                var new_password = $("#newuserpsw").val();
                var password_again = $("#confirmuserpsw").val();
                var index1 = openLoadShade();
                $.ajax({
                    url: "/user/editpsw_save",
                    data: {
                        olduserpsw:     old_password,
                        newuserpsw:     new_password,
                        confirmuserpsw: password_again,
                        _token:         "{{ csrf_token() }}",
                    },
                    dateType: "JSON",
                    type: "POST",
                    async: false,
                    success: function(data) {
                        closeLoadShade(index1);
                        if(data.msg == "SUCCESS") {
                            layer.msg("更改成功!", {
                                time: 20000, //20s后自动关闭
                                btn: ['知道了'],
                                yes: function (index, layero) {
                                    layer.closeAll();
                                    top.location = '/user/loginOut';
                                }
                            });
                        } else if (data.msg == "FAIL") {
                            if(data.err == "localpswerr") {
                                errorTips("旧密码错误!", "msg", data.col);
                            } else if(data.err == "apipswerr") {
                                errorTips("旧密码错误!", "msg", data.col);
                            } else if(data.err == "UPDATEFAIL") {
                                errorTips("修改失败,请重新操作!", "msg", "");
                            } else if(data.err == "FATALCANOTCONNECT") {
                                errorTips("网络故障,密码修改失败,请稍后重试!", "msg", "");
                            }
                        }
                    },
                    error:function(data) {
                        closeLoadShade(index1);
                        alert("未知错误，请联系客服或重新操作!");
                    }
                });
            }
        }
    </script>
@endsection