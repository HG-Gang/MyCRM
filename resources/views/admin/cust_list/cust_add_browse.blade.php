@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
	<form class="layui-form" action="" id="CustomerAddForm" style="margin-top: 8px;">
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
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">邀请码</label>
				<div class="layui-input-block">
					<input type="text" name="userInviterId" id="userInviterId" autocomplete="off" placeholder="请输入邀请码" class="layui-input" style="width: 200px;">
				</div>
			</div>
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
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">密码</label>
				<div class="layui-input-block">
					<input type="password" name="password" id="password" autocomplete="off" placeholder="请输入密码" class="layui-input" style="width: 200px;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button type="button" class="layui-btn" onclick="customerAdd()">立即提交</button>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
			</div>
		</div>
	</form>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function customerAdd() {
			console.log(getFormData("CustomerAddForm"));
			if (username() && userInviterId() && check_user_grp() && password()) {
				
				var index1 = openLoadShade();
				$.ajax({
					url: route_prefix() + "/cust/cust_save_add",
					data: {
						data:                   getFormData("CustomerAddForm"),
						usergrpName:            $("#usergrpId option:selected").text(),
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
							} else if (data.err == "Invalidgrp") {
								errorTips("无效的用户组别!", "msg", data.col);
							} else {
								layer.msg("开户失败", {
									time: 20000, //20s后自动关闭
									btn: ['知道了'],
									yes: function (index, layero) {
										parent.layer.closeAll();
										window.location.href = "{{url(route_prefix()  .'/cust/add')}}";
									},
									end: function () {
										parent.layer.closeAll();
										window.location.href = "{{url(route_prefix() . '/cust/add')}}";
									}
								});
							}
						} else if (data.msg == "SUC") {
							layer.msg("开户成功", {
								time: 20000, //20s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									parent.layer.closeAll();
									window.location.href = "{{url(route_prefix() . '/cust/add')}}";
								},
								end: function () {
									parent.layer.closeAll();
									window.location.href = "{{url(route_prefix() . '/cust/add')}}";
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