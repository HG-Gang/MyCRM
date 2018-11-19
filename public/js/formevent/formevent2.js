layui.config({
	base: '../js/plugins/layui/layadmin/lay/modules/',
	version: true, //用于设置浏览器是否缓存， true 不缓存，false,缓存
}).use(['form', 'layer'], function() {
	var form    = layui.form;
	var layer   = layui.layer;
	$("[name=agreeRule]:checkbox").prop("checked", false);
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	form.on("checkbox(agreeRule)", function (data) {
		if (data.elem.checked) {
			$("#btnregister").removeClass("layui-btn-disabled");
			form.verify({
				username: function (value, item) {
					if($("#agreeRule").is(":checked")) {
						if (value == '') {
							return '用户名必填!';
						} else if (value.length > 60) {
							return '请输入30个以内的汉字';
						} else if(!new RegExp("^[\u4e00-\u9fa5\\s·]+$").test(value)){
							return '用户名只能是中文!';
						}
					}
				},
				password: function (value, item) {
					var pswReg = /^[a-zA-Z][\w\W]*\d$/;
					if($("#agreeRule").is(":checked")) {
						if (value == '') {
							return '请输入密码!';
						} else if (value.length < 6) {
							return '密码长度必须大于6';
						} else if (!pswReg.test(value)) {
							return '密码开首必须是字母,且以数字结尾!';
						}
					}
				},
				againpassword: function (value, item) {
					if($("#agreeRule").is(":checked")) {
						if (value == '') {
							return '请再次输入确认密码!';
						} else if (value != $("#password").val()) {
							return '两次密码不一样!';
						}
					}
				},
				userIdcardNo: function (value, item) {
					if($("#agreeRule").is(":checked")) {
						var isCardReg = /(^\d{15}$)|(^\d{17}(\d|X)$)/;
						var dateReg = /^(19|20)\d{2}-(1[0-2]|0?[1-9])-(0?[1-9]|[1-2][0-9]|3[0-1])$/;
						if (value == '') {
							return '请输入身份证号!';
						} else if (!isCardReg.test(value)) {
							return '证件号长度必须为15或18位，或最后一位字母必须大写!';
						} else {
							var sBirthday=value.substr(6,4)+"-"+Number(value.substr(10,2))+"-"+Number(value.substr(12,2));
							var age = new Date().getFullYear() - value.substr(6,4);
							if (aCity[parseInt(value.substr(0,2))]== null) {
								return '身份证号码地区未知,请核对';
							} else if (!dateReg.test(sBirthday)) {
								return '身份证号出生日期有误!';
							} else if (!(age >= 18 && age <= 70)) {
								return '年龄必须在18-70岁之间!';
							}
						}
					}
				},
				userphoneNo: function (value, item) {
                    var phoneReg = /^1(3|4|5|7|8)\d{9}$/;
					if($("#agreeRule").is(":checked")) {
						if (value == '') {
							return '请输入手机号!';
						} else if (value.length != 11) {
							return '请输入11位有效手机号!';
						} /*else if (!phoneReg.test(value)) {
							return '手机号必须以13,14,15,17,18开头!';
						}*/
					}
				},
				useremail: function (value, item) {
					if($("#agreeRule").is(":checked")) {
						if (value == '') {
							return '请输入邮箱地址!';
						} else if (!new RegExp("^[a-z]|[0-9]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$").test(value)) {
							return '邮箱格式错误'
						}
					}
				},
				userInviterId: function (value, item) {
					var is_invite = $.trim($("#is_invite").val());
					if($("#agreeRule").is(":checked")) {
						var last_char = value.substr(-1, 1).charCodeAt();
						if (value == '') {
							return '请输入邀请码!';
						} else if (value.length < 4 || value.length > 8) {
							return '请输入非零开首的4-8位有效邀请码!';
						} else if (is_invite = "FALSE" && last_char > 65) {
							return '请输入有效的邀请码!';
						} /*else if (!new RegExp("^[1-9]{3, 7}$").test(value)) {
							return '请输入有效的数字账号';
						}*/
					}
				},
				/*userverfcode: function (value, item) {
					//if($("#agreeRule").is(":checked")) {
						if (value == '' && $("#agreeRule").is(":checked")) {
							return '请输入验证码!';
						}
					//}
				},*/
			});
			
			form.on("submit(btnregister)", function(data) {
				if ($.trim($("#userverfcode").val()) == "") {
					errorTips("请输入验证码", "msg", "userverfcode");
				} else {
					var index1 = openLoadShade();
					$.ajax({
                        url: '/user/register/registerinto',
                        type: 'POST',
                        dataType: 'JSON',
                        data: data.field,
                        error: function (msg) {
                            layer.msg('网络故障,请稍后再登录.');
                        },
                        success: function (msg) {
                            closeLoadShade(index1);
                            if (msg.status == 'FAIL') {
                                if (msg.errorType == 'TipsParentId') {
                                    errorTips('该邀请码的上级代理是: ' +  msg.col, 'msg', 'userInviterId');
                                } else if (msg.errorType == 'ErrorParentId') {
                                    errorTips('无效的邀请码', 'msg', 'userInviterId');
                                } else if (msg.errorType == 'ErrorCommProp') {
                                    errorTips('该邀请码不能发展无佣金客户', 'msg', 'userInviterId');
                                } else if (msg.errorType == 'ErrorParentIdNoConfirm') {
                                    errorTips('该邀请码代理级别未确认,无法邀请注册!', 'msg', 'userInviterId');
                                } else if (msg.errorType == 'ErrorverifCode') {
                                    errorTips('验证码错误', 'msg', 'userverfcode');
                                } else if (msg.errorType == 'ErrorEmail') {
                                    errorTips('输入的邮箱地址与接收的邮箱地址不一致!', 'msg', 'useremail');
                                } else if (msg.errorType == 'ErrorphoneNo') {
                                    errorTips('输入的手机号与接收验证码的手机号不一致!', 'msg', 'userphoneNo');
                                } else if (msg.errorType == 'ErrorMT4Async') {
                                    errorTips(msg.status, 'dialog', msg.col);
                                }
                            } else if (msg.status == 'SUCCESS') {
                                //errorTips('SUCCESS', 'dialog', {"Uid":1001, "psw":'123456'});
                                errorTips(msg.status, 'dialog', msg.col);
                            }
                        }
                    });
				}
					return false;
			});
		} else {
			$("#btnregister").addClass("layui-btn-disabled");
			form.on("submit(btnregister)", function(data) {
				return false;
			});
		}
	});
	
	form.on("submit(getVerifyCode)", function (data) {
		if ($("#getVerifyCode").hasClass("layui-btn-disabled")) return;
		console.log(data.field);
		if(userIdcardNo() && userphoneNo() && useremail() && userInviterId()) {
			$.ajax({
				url: '/user/register/registerVerifyInfo',
				type: 'POST',
				dataType: 'JSON',
				data: {
					userIdcardNo:       data.field.userIdcardNo,
					userphoneNo:        data.field.userphoneNo,
					useremail:          data.field.useremail,
					userInviterId:      data.field.userInviterId,
					modules:            data.field.modules,
					verifyType:         data.field.verifyType,
					is_invite:          data.field.is_invite,
				},
				error: function (msg) {
					layer.msg('网络故障,请稍后再登录.');
				},
				success: function (msg) {
					console.log(msg);
					if (msg.status == 'FAIL') {
						if (msg._ido == 'userIdcardNo') {
							errorTips('身份证号码已存在!', 'msg', 'userIdcardNo');
						} else if (msg._tel == 'userphoneNo') {
							errorTips('手机号已存在!', 'msg', 'userphoneNo');
						} else if (msg._eml == 'useremail') {
							errorTips('邮箱已存在!', 'msg', 'useremail');
						} else if (msg._iId == 'userInviterId') {
							errorTips('无效的邀请码!', 'msg', 'userInviterId');
						}
					} else if (msg.status == 'SUC') {
						/*验证通过，开始发送验证码*/
						verifyPassSendCode(data);
					}
				}
			});
		}
	});
});

function mianzeSM() {
	layer.open({
		title: ["免责申明", "background:#009688; color: #fff"],
		type: 1,
		shade: [0.6, '#393D49'],
		area: ['900px', '700px'],
		anim: 0,
		move: false,
		content: $("#mianzeSM"),
		resize: false,
	});
}

function fengxianPL() {
	layer.open({
		title: ["风险披露", "background:#009688; color: #fff"],
		type: 1,
		shade: [0.6, '#393D49'],
		area: ['900px', '700px'],//['1100px', '850px'],
		anim: 0,
		move: false,
		content: $("#fengxianPL"),
		resize: false,
	});
}

function custAgerrement() {
	layer.open({
		title: ["客户协议书", "background:#009688; color: #fff"],
		type: 1,
		shade: [0.6, '#393D49'],
		area: ['900px', '700px'],
		anim: 0,
		move: false,
		content: $("#custAgerrement"),
		resize: false,
	});
}

function attachDownload() {
	alert('稍等...');
}

function getcheckBox() {
	var isChecked = false;
	$.each($("#registerForm").find("input,select,textarea"), function (e, i) {
		if ((/^checkbox|radio$/.test(i.type)) && i.id == 'agreeRule' && i.checked) {
			return true;
		}
	});
	
	return isChecked;
}

function verifyPassSendCode(data) {
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
			url: '/user/register/registerSendCode',
			type: 'POST',
			dataType: 'JSON',
			data: {
				userIdcardNo:       data.field.userIdcardNo,
				userphoneNo:        data.field.userphoneNo,
				useremail:          data.field.useremail,
				userInviterId:      data.field.userInviterId,
				modules:            data.field.modules,
				verifyType:         data.field.verifyType,
				is_invite:          data.field.is_invite,
			},
			error: function (msg) {
				countdown = 0;
				_this.removeClass("layui-btn-disabled").html("获取验证码");//启用按钮
				layer.msg('网络故障,请稍后再注册.');
			},
			success: function (msg) {
				console.log('CODE' + msg);
				if (msg._ido == 'userIdcardNo') {
					errorTips('身份证号码已存在!', 'msg', 'userIdcardNo');
				} else if (msg._tel == 'userphoneNo') {
					errorTips('手机号已存在!', 'msg', 'userphoneNo');
				} else if (msg._eml == 'useremail') {
					errorTips('邮箱已存在!', 'msg', 'useremail');
				} else if (msg._iId == 'userInviterId') {
					errorTips('无效的邀请码!', 'msg', 'userInviterId');
				} else if (msg.status == false) {
					console.log(msg.status);
					countdown = 0;
					_this.removeClass("layui-btn-disabled").html("获取验证码");//启用按钮
					//errorTips('发送失败', 'tips', 'getVerifyCode');
					layer.msg('发送失败');
				} else if (msg.status == true) {
					console.log(msg.status);
					//errorTips('发送成功', 'tips', 'getVerifyCode');
					layer.msg('发送成功');
				}
			}
		});
	}
}
