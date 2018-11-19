@extends('user.layout.main_right')

@section('public-resources')
    <style>
        .a-upload{padding: 4px 10px;height: 27px;line-height: 28px;position: relative;top: 12px;left: -14px;cursor: pointer;color: #888;background: #fafafa;border: 1px solid #ddd;border-radius: 4px;overflow: hidden;display: inline-block;*display: inline;*zoom: 1;}
        .a-upload input{position: absolute;font-size: 100px;right: 0;top: 0;opacity: 0;filter: alpha(opacity=0);cursor: pointer;}
        .a-upload:hover{color: #444;background: #eee;border-color: #ccc;text-decoration: none;}
    </style>
@endsection

@section('content')
    <div>
        <ol style="background: #fef7e4; text-indent: 1em;font-size: 13px;">
            <li>1. 有且仅有账户当前出金申请不是待审核状态的订单, 才能提交申请.</li>
            <li>2. 有且仅有账户银行卡审核通过, 才能提交申请.</li>
            <li>3. 满足以上两点才能再次提交申请变更银行卡信息.</li>
        </ol>
    </div>
    <form class="layui-form" action="" id="UserInfoForm" style="margin-top: 8px;">
        <div class="layui-form-item" enctype="multipart/form-data">
            <div class="layui-inline">
                <label class="layui-form-label">持卡人</label>
                <div class="layui-input-block">
                    <input type="text" name="username" id="username" value="{{ $_user_info['user_name'] }}" autocomplete="off" placeholder="请输入持卡人名称" class="layui-input" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">开户银行</label>
                <div class="layui-input-block">
                    <input type="text" name="bankclass" id="bankclass" autocomplete="off" placeholder="请输入开户银行" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">银行卡号</label>
                <div class="layui-input-block">
                    <input type="text" name="bankno" id="bankno" autocomplete="off" placeholder="请输入银行卡号" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">开户支行</label>
                <div class="layui-input-block">
                    <input type="text" name="bankinfo" id="bankinfo" autocomplete="off" placeholder="请输入省、市、区(县)、支行" class="layui-input" style="width: 270px;">
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">卡号正面照</label>
                <div class="layui-input-block">
                    <input type="text" name="bankimg" id="bankimg" autocomplete="off" class="layui-input" placeholder="支持JPG,JPEG,PNG格式且小于2M" readonly="readonly" style="width: 270px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <a href="javascript:void(0);" class="a-upload"><input type="file" name="file_img" id="file_img" onchange="BankCardfillFile()">选择文件</a>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">手机号</label>
            <div class="layui-input-block">
                <input type="text" name="userphoneNo" id="userphoneNo" placeholder="{{ substr_replace(substr($_user_info['phone'], (stripos($_user_info['phone'], '-') + 1)), '*****', 3, -3) }}" autocomplete="off" class="layui-input" style="width: 270px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">验证码</label>
            <div class="layui-input-inline">
                <input type="text" id="userverfcode" name="userverfcode" maxlength="6" placeholder="请输入验证码" autocomplete="off" class="layui-input" style="width: 163px;">
                <button type="button" id="getVerifyCode" onclick="funcChangeBankGetVerifyCode()" class="layui-btn" style="margin-left: 163px;margin-top: -54px;">获取验证码</button>
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
                <button type="button" class="layui-btn" onclick="ajaxUploadAuthChangeBankCard()">提交资料</button>
            </div>
        </div>
    </form>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function BankCardfillFile() {
			var bankCard = $("input[name='file_img']").val().lastIndexOf("\\");
			$("#bankimg").val($("input[name='file_img']").val().substr(bankCard + 1));
		}
		
		function funcChangeBankGetVerifyCode() {
			if (!$("#getVerifyCode").hasClass("layui-btn-disabled")) {
				if (userphoneNo()) {
					$.ajax({
						url: '/user/center/changeBankCardVerifyCode',
						type: 'POST',
						dataType: 'JSON',
						data: {
							userphoneNo:		$.trim($("#userphoneNo").val()),
                            _token:				"{{ csrf_token() }}",
						},
						error: function (msg) {
							layer.msg("网络故障,请稍后再操作", {icon: 5, shift: 6});
						},
						success: function (msg) {
							console.log(msg);
							if (msg.msg == 'FAIL') {
								if (msg.err == 'userphoneNo') {
								errorTips('手机号错误!', 'msg', data.col);
								}
							} else if (msg.msg == 'SUC') {
								/*验证通过，开始发送验证码*/
								ChangeBankCardverifyPassSendCode();
							}
						}
					});
				}
			}
		}
		
		function ChangeBankCardverifyPassSendCode() {
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
				url: '/user/center/changeBankCardSendCode',
				type: 'POST',
				dataType: 'JSON',
				data: {
					userphoneNo:		$.trim($("#userphoneNo").val()),
					type:				"{{ $type }}",
					_token:				"{{ csrf_token() }}",
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
		
		function ajaxUploadAuthChangeBankCard() {
			var formData = new FormData();
			formData.append("bankclass", $("#bankclass").val());
			formData.append("bankinfo", $("#bankinfo").val());
			formData.append("userphoneNo", $("#userphoneNo").val());
			formData.append("userverfcode", $("#userverfcode").val());
			formData.append("password", $.trim($("#password").val()));
			formData.append("_token", "{{ csrf_token() }}");
			formData.append("bankno", $("#bankno").val());
			formData.append("bankimg", $("#file_img")[0].files[0]); //正面照
			formData.append("uploadType", "{{ $type }}");
			
			if (checkphotoBankCard() && checkbankclass() && checkbankNo() && checkbankinfo() && userphoneNo() && userverfcode() && password()) {
				var index = openLoadShade();
				$.ajax({
					url: "/user/center/uploadChangeBankCard",
					data: formData,
					processData: false,
					contentType: false,
					dateType: "JSON",
					type: "POST",
					async: false,
					success: function(data) {
						closeLoadShade(index);
						if (data.msg == "FAIL") {
							if (data.err == "errbankpendingauth") {
								errorTips("当前银行卡正在审核中,无法进行变更!", "msg", data.col);
							} else if (data.err == "errisapplying") {
								errorTips("当前有出金订单等待处理,无法进行变更!", "msg", data.col);
							} else if (data.err == "errpassword") {
								errorTips("密码错误!", "msg", data.col);
							} else if (data.err == "erruserphoneNo") {
								errorTips("输入的手机号与接收验证码的手机号不一致!", "msg", data.col);
							} else if (data.err == "erruserverfcode") {
								errorTips("验证码错误!", "msg", data.col);
							} else if (data.err == "POSOVERSIZE1") {
								errorTips("正面照不能超过2M", "msg", data.col);
							} else if (data.err == "POSERRORFORMAT") {
								errorTips("请上传支持的图片格式!", "msg", data.col);
							} else if (data.err == "uploadErr") {
								console.log(data.col);
								layer.msg("图片上传失败", {icon: 5, shift: 6});
							} else if (data.err == "UPDATEFAIL") {
								errorTips("上传失败, 请稍后再试", "msg", data.col);
							}
						} else if (data.msg == "SUC") {
							layer.msg("资料上传成功", {
								time: 200000, //200s后自动关闭
								btn: ['知道了'],
								yes: function (index, layero) {
									parent.layer.closeAll();
									top.location = '/user/index';
								}
							});
						}
					},
					error: function (data) {
						closeLoadShade(index);
						errorTips("单张图片不能超过2M", "msg");
					}
				});
			}
		}
	</script>
@endsection