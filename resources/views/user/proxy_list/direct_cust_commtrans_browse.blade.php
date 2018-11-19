@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
	<form class="layui-form" action="" id="proxyListForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">转佣账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" value="{{ $uid }}" placeholder="请输入转佣账户" class="layui-input" disabled="" style="width: 200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">转佣金额</label>
				<div class="layui-input-block">
					<input type="text" name="comm_money" id="comm_money" autocomplete="off" placeholder="可用金额 {{ $_user_info['user_money'] }}" class="layui-input" style="width: 200px;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易密码</label>
				<div class="layui-input-block">
					<input type="password" name="password" id="password" autocomplete="off" placeholder="请输入交易密码" class="layui-input" style="width: 200px;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button type="button" class="layui-btn" onclick="proxyDirectCustomerCommTrans()">确定</button>
			</div>
		</div>
	</form>
@endsection

@section('custom-resources')
	<script>
		function proxyDirectCustomerCommTrans() {
			var comm_money = $.trim($("#comm_money").val());
			var password = $.trim($("#password").val());
			
			if (comm_money == "") {
				layer.msg("转佣金额为必填选项!", {icon: 5, shift: 6});
				$("#comm_money").addClass("layui-form-danger").focus();
			} else if (Number(comm_money) > Number("{{ $_user_info['user_money'] }}")) {
				layer.msg("转佣金额不能大于可用金额!", {icon: 5, shift: 6});
				$("#comm_money").addClass("layui-form-danger").focus();
			} else if (password == "") {
				layer.msg("密码为必填选项!", {icon: 5, shift: 6});
				$("#password").addClass("layui-form-danger").focus();
			} else {
				var openLoadShadeIndex = openLoadShade();
				$.ajax({
					url: '/user/proxy/directUserCommTrans',
					data: {
						depositId: "{{ $uid }}",
						comm_money: comm_money,
						password: password,
						_token: "{{ csrf_token() }}",
					},
					dateType: "JSON",
					type: "POST",
					async: false,
					success: function (data) {
						closeLoadShade(openLoadShadeIndex);
						if(data.msg == "FAIL") {
							if(data.errorType == "ErrorPassword") {
								layer.tips("交易密码错误", $("#password"), {tips: [1, "#335b9f"], time: 5000});
								return;
							}
							if(data.errorType == "MT4_data_no_sync") {
								layer.msg("因网络原因，转佣金额未能成功，请重新操作!", {icon: 5, shift: 6});
								return;
							}
							if(data.errorType == "_CONNECT_FAILED_") {
								layer.msg("佣金转户操作失败，请重新操作!", {icon: 5, shift: 6});
								return;
							}
							if(data.errorType == "NOTALLOW") {
								layer.msg("抱歉，您已被限定不允许出金!", {icon: 5, shift: 6});
								return;
							}
						} else if(data.msg == "SUCCESS"){
							layer.msg('佣金转户操作成功', {
								time: 0, //不自动关闭
								shade: 0.4,
								btn: ["知道了"],
								yes: function(index){
									layer.close(index);
									parent.layer.closeAll();
								}
							});
						}
					},
					error: function (data) {
						closeLoadShade(openLoadShadeIndex);
						layer.msg("系统错误, 请稍后重新操作");
						return;
					}
				});
			}
		}
	</script>
@endsection