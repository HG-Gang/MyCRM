@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
	<form class="layui-form" id="userAuthForm" action="" style="margin-top: 20px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">账户ID</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" value="{{ $_info['user_id'] }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">账户姓名</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" value="{{ $_info['user_name'] }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">手机号码</label>
				<div class="layui-input-block">
					<input type="text" id="userphoneNo" name="phoneNo" value="{{ substr($_info['phone'], (stripos($_info['phone'], '-') + 1)) }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">邮箱</label>
				<div class="layui-input-block">
					<input type="text" id="useremail" name="useremail" value="{{ $_info['email'] }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">身份证号码</label>
				<div class="layui-input-block">
					<input type="text" id="userIdcardNo" name="userIdcardNo" value="{{ $_info['IDcard_no'] }}" autocomplete="off" disabled="" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">银行卡号</label>
				<div class="layui-input-block">
					<input type="text" id="userIdcardNo" name="userbankNo" value="{{ $_info['bank_no_tmp'] }}" autocomplete="off" disabled="" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">开户银行</label>
				<div class="layui-input-block">
					<input type="text" id="bank_class_tmp" name="bank_class_tmp" value="{{ $_info['bank_class_tmp'] }}" autocomplete="off" disabled="" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">支行地址</label>
				<div class="layui-input-block">
					<input type="text" id="bank_info_tmp" name="bank_info_tmp" value="{{ $_info['bank_info_tmp'] }}" autocomplete="off" disabled="" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div id="layer-photos-img" class="layer-photos-demo" style="margin-left: 30px;">
				@if($_info['IDcard_status'] == '1')
					<img layer-pid="img_idcard1" layer-src="{{ URL::asset($_info['img'][0]['img_idcard01_path']) }}" src="{{ URL::asset($_info['img'][0]['img_idcard01_path']) }}" alt="身份证正面照" style="width: 100px; height: 100px;">
					<img layer-pid="img_idcard2" layer-src="{{ URL::asset($_info['img'][0]['img_idcard02_path']) }}" src="{{ URL::asset($_info['img'][0]['img_idcard02_path']) }}" alt="身份证反面照" style="width: 100px; height: 100px;">
				@endif
				@if($_info['bank_status'] == '1')
					<img layer-pid="img_bank" layer-src="{{ URL::asset($_info['img'][0]['img_bank_path']) }}" src="{{ URL::asset($_info['img'][0]['img_bank_path']) }}" alt="银行卡" style="width: 100px; height: 100px;">
				@endif
			</div>
		</div>
		@if($_info['IDcard_status'] == '1')
			<div class="layui-form-item">
				<label class="layui-form-label">身份证审核</label>
				<div class="layui-input-block">
					<input type="radio" name="idcard_auth_status" value="0" title="通过">
					<input type="radio" name="idcard_auth_status" value="1" title="不通过" checked>
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">审核原因</label>
				<div class="layui-input-block">
					<input type="text" id="idcar_reason" name="idcar_reason" autocomplete="off" placeholder="请输入身份证审核不通过原因" class="layui-input" style="width: 400px;">
				</div>
			</div>
		@endif
		@if($_info['bank_status'] == '1')
			<div class="layui-form-item">
				<label class="layui-form-label">银行卡审核</label>
				<div class="layui-input-block">
					<input type="radio" name="bank_auth_status" value="0" title="通过">
					<input type="radio" name="bank_auth_status" value="1" title="不通过" checked>
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">审核原因</label>
				<div class="layui-input-block">
					<input type="text" id="bank_reason" name="bank_reason" autocomplete="off" placeholder="请输入银行卡审核不通过原因" class="layui-input" style="width: 400px;">
				</div>
			</div>
		@endif
		@if($_info['IDcard_status'] == '1' || $_info['bank_status'] == '1')
			<div class="layui-form-item">
				<div class="layui-input-block">
					<button type="button" class="layui-btn" onclick="auth_user()">确认</button>
				</div>
			</div>
		@endif
	</form>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function idcardAuthStatus() {
			if ("{{ $_info['IDcard_status'] }}" != '1') return true;
			var val = $("input[name='idcard_auth_status']:checked").val();
			if (val == '1') {
				//检查审核原因是否为空
				if ($("#idcar_reason").val() == '') {
					errorTips("审核不通过时, 不通过原因必填!", "msg", "idcar_reason");
				} else {
					return true;
				}
			} else {
				return true;
			}
		}
		
		function bankAuthStatus() {
			if ("{{ $_info['bank_status'] }}" != '1') return true;
			var val = $("input[name='bank_auth_status']:checked").val();
			if (val == '1') {
				//检查审核原因是否为空
				if ($("#bank_reason").val() == '') {
					errorTips("审核不通过时, 不通过原因必填!", "msg", "bank_reason");
				} else {
					return true;
				}
			} else {
				return true;
			}
		}

		function auth_user() {
			if (idcardAuthStatus() && bankAuthStatus()) {
				var idcard = "",bank ="", idcard_reason = "", bank_reason = "";
				if ("{{ $_info['IDcard_status'] }}" == "1") idcard = $("input[name='idcard_auth_status']:checked").val();
				if ("{{ $_info['bank_status'] }}" == "1") bank = $("input[name='bank_auth_status']:checked").val();
				if (idcard == "1") {
                    idcard_reason = $("#idcar_reason").val();
                }
                if (bank == "1") {
                    bank_reason = $("#bank_reason").val();
                }
				var index1 = openLoadShade();
				$.ajax({
					url: route_prefix() + "/auth/user_idcard_bank",
					data: {
						userId: 				$("#userId").val(),
						username:				$("#username").val(),
						idcard_auth:			idcard,
						bank_auth:				bank,
						userIdcard_status:		"{{ $_info['IDcard_status'] }}",
						userbank_status:		"{{ $_info['bank_status'] }}",
                        idcard_reason:			idcard_reason,
                        bank_reason:			bank_reason,
						_token:					"{{ csrf_token() }}",
					},
					dateType: "JSON",
					type: "POST",
					async: false,
					success: function(data) {
						closeLoadShade(index1);
						if (data.msg == "FAIL") {
							layer.msg("操作失败,请稍后重试!", {icon: 5, shift: 6});
						} else if (data.msg == "SUC") {
							layer.msg("操作成功", {
								time: 20000, //20s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									parent.layer.closeAll();
									parent.window.location.href = "{{url(route_prefix() . '/auth/user_examine')}}";
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