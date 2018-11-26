var aCity   = {
	11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江",
	31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",
	50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"
};
(function ($) {
	$.fn.extend({
		ajaxUrl: function (op) {
			if ($.isEmpty(op.targetId)) {
				op.targetId = "contentId";
			}
			$.ajax({
				url:op.url + "?timeStamp=" + new Date().getTime(),
				type: op.type,
				dataType: 'html',
				success: function (data) {
					$("#" + op.targetId).html(data);
				},
				error: function (data) {
					alert("加载失败，请稍后重试");
				}
			});
		}
	});
	
	$.extend({
		strTrim : function(str) {
			if (str == undefined || str == null) {
				str = "";
			}
			return $.trim(str);
		},
		isEmpty : function(str) {
			if (str == undefined || str == null || $.trim(str) == "") {
				return true;
			}
			return false;
		}
	});
})(jQuery);

//'form', 'layedit'
layui.use(['form', 'laydate', 'element'], function(){
	var form = layui.form,
		element = layui.element,
	     layer = layui.layer,
		//,layedit = layui.layedit
		laydate = layui.laydate;

	layer.photos({
		photos: '#layer-photos-img',
		shift: 5, //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
	});
	//日期
	laydate.render({elem: '#startdate',});
	laydate.render({elem: '#enddate',});
	laydate.render({elem: '#deposit_startdate',});
	laydate.render({elem: '#deposit_enddate',});
	laydate.render({elem: '#withdraw_startdate',});
	laydate.render({elem: '#withdraw_enddate',});
	laydate.render({elem: '#withdraw_apply_startdate',});
	laydate.render({elem: '#withdraw_apply_enddate',});
	laydate.render({elem: '#direct_deposit_startdate',});
	laydate.render({elem: '#direct_deposit_enddate',});
	laydate.render({elem: '#direct_withdraw_startdate',});
	laydate.render({elem: '#direct_withdraw_startdate',});
	
	//监听Tab切换
	element.on('tab(customer_flow)', function() {
		// && role_type == 'Agents'
		var tabpanel = $(this).data("tab_id");
		if(tabpanel == "deposit_flow") {
			deposit_flow();
		} else if(tabpanel == "withdrawal_flow") {
			withdrawal_flow();
		} else if(tabpanel == "withdrawal_apply_flow") {
			withdrawal_apply_flow();
		} else if(tabpanel == "direct_deposit_flow") {
			direct_deposit_flow();
		} else if(tabpanel == "direct_withdrawal_flow") {
			direct_withdrawal_flow();
		}
	});

	//添加代理，下拉监听事件
	form.on('select(trands_mode)', function(data){
		console.log(data.value); //得到被选中的值
		init_select_status();
	});
});

function route_prefix() {
	return '/pada/admin';
}

/**
 * 获取浏览器版本,名字
 */
function myBrowserInfo() {
	var browser = {appname: 'unknown', version: 0};
	// 使用navigator.userAgent来判断浏览器类型
	var userAgent = window.navigator.userAgent.toLowerCase();
	alert(userAgent);
    //msie,firefox,opera,chrome,netscape
	if ( /(msie|firefox|opera|chrome|netscape)\D+(\d[\d.]*)/.test( userAgent ) ) {
		browser.appname = RegExp.$1;
		browser.version = RegExp.$2;
	} else if ( /version\D+(\d[\d.]*).*safari/.test( userAgent ) ) {
		// safari
		browser.appname = 'safari';
		browser.version = RegExp.$2;
	}
	
	return browser;
}

function uploadIDcard1() {
	console.log($(".clildFrame .layui-tab-item.layui-show").find("iframe")[0]);
	/*$(".clildFrame .layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);*/
	/*layer.open({
		type: 1,
		title: '提示信息',
		skin: 'layui-layer-molv',
		closeBtn: 0,
		area: ['450px', '240px'],
		btn: ["知道了"],
		btnAlign: 'c',
		content: 'hahahaha',
		yes: function (index, layero) {
			if (errText == 'SUCCESS') {
				top.location = '/user/index';
			} else if (errText == 'FAIL') {
				top.location = '/';
			}
		},
	});*/
}

function ajaxGetDataGridTableTitle() {
	alert('waiting...');
}

function parseFloatToFixed(value, number) {
	if (isNaN(number)) number = 2;
	return parseFloat(value).toFixed(number);
}

//公共验证信息
function errorTips(errText, msgType, id) {
	if (msgType == "msg") {
		layer.msg(errText, {icon: 5, shift: 6});
		$("#" + id).addClass("layui-form-danger").focus();
		return;
	} else if (msgType == "tips") {
		layer.tips(errText, $("#" + id), {
			tips: [2, '#1AA094'],
			time: 4000
		});
		$("#" + id).addClass("layui-form-danger").focus();
		return;
	} else if (msgType == "dialog") {
		layer.open({
			type: 1,
			title: '提示信息',
			skin: 'layui-layer-molv',
			closeBtn: 0,
			area: ['450px', '240px'],
			btn: ["知道了"],
			btnAlign: 'c',
			content: createHtml(errText, id),
			yes: function (index, layero) {
				if (errText == 'SUCCESS') {
					top.location = '/user/index';
				} else if (errText == 'FAIL') {
					top.location = '/';
				}
			},
		});
	}
}

function createHtml(status, data) {
	var html = '';
	
	html = '<div>'
	if (status == 'SUCCESS') {
		html += '<h4 style="font-size: 17px;color: #333;text-align: center; margin-top: 10px;">恭喜您注册成功，您可以使用此账号登录MT4交易平台</h4>';
	} else if (status == 'FAIL') {
		html += '<h4 style="font-size: 17px;color: #333;text-align: center; margin-top: 10px;"><p>很抱歉，因网络或其他原因，未能与MT4同步信息</p><p>请自己登陆本系统即可在线同步注册</p></h4>';
	}
	
	html += '<ul style="text-align: center; margin-top: 10px;"><li><span>您的交易账户: </span><span style="font-weight: 900; font-size: 22px; color: #0C0C0C;">' + data.Uid + '</span></li>';
	html += '<li><span style="margin-top: 10px;">您的交易密码: </span><span style="font-weight: 500; color: #00a0e9;">' + data.psw + '</span></li>'
	html += '</ul></div>';
	
	return html;
}

function username() {
	var username = $.trim($("#username").val());
	if (username == '') {
		errorTips('用户名必填!', 'msg', 'username');
	} else if (username.length > 60) {
		errorTips('请输入30个以内的汉字!', 'msg', 'username');
	} else if(!new RegExp("^[\u4e00-\u9fa5\\s·]+$").test(username)){
		errorTips('用户名只能是中文!', 'msg', 'username');
	} else {
		return true;
	}
}

function password() {
	var password = $.trim($("#password").val());
    var pasReg = /^[a-zA-Z][\w\W]*\d$/;
	if (password == '') {
		errorTips('请输入密码!', 'msg', 'password');
	} else if (password == "********") {
        return true;
	} else if (password.length < 6) {
		errorTips('密码长度必须大于6!', 'msg', 'password');
	} else if (!pasReg.test(password)) {
		errorTips('密码开首必须是字母,且以数字结尾!', 'msg', 'password');
	} else {
		return true;
	}
}

function againpassword() {
	var againpassword = $.trim($("#againpassword").val());
	if (againpassword == '') {
		errorTips('请再次输入确认密码!', 'msg', 'againpassword');
	} else if (againpassword != $.trim($("#password").val())) {
		errorTips('两次密码不一样!', 'msg', 'againpassword');
	} else {
		return true;
	}
}

function userIdcardNo() {
	var userIdcardNo = $.trim($("#userIdcardNo").val());
	var isCardReg = /(^\d{15}$)|(^\d{17}(\d|X)$)/;
	var dateReg = /^(19|20)\d{2}-(1[0-2]|0?[1-9])-(0?[1-9]|[1-2][0-9]|3[0-1])$/;
	if (userIdcardNo == '') {
		errorTips('请输入身份证号!', 'msg', 'userIdcardNo');
	} else if (!isCardReg.test(userIdcardNo)) {
		errorTips('证件号长度必须为15或18位，或最后一位字母必须大写!', 'msg', 'userIdcardNo');
	} else {
		var sBirthday=userIdcardNo.substr(6,4)+"-"+Number(userIdcardNo.substr(10,2))+"-"+Number(userIdcardNo.substr(12,2));
		var age = new Date().getFullYear() - userIdcardNo.substr(6,4);
		if (aCity[parseInt(userIdcardNo.substr(0,2))]== null) {
			errorTips('错误的身份证号码,请核对!', 'msg', 'userIdcardNo');
		} else if (!dateReg.test(sBirthday)) {
			errorTips('身份证号出生日期有误!', 'msg', 'userIdcardNo');
		} else if (!(age >= 18 && age <= 70)) {
			errorTips('年龄必须在18-70岁之间!', 'msg', 'userIdcardNo');
		} else {
			return true;
		}
	}
}

function userphoneNo() {
	//var phoneReg = /^1\d{10}$/;
	var phoneReg = /^1(3|4|5|7|8)\d{9}$/;
	var userphoneNo = $("#userphoneNo").val();
	if (userphoneNo == '') {
		errorTips('请输入手机号!', 'msg', 'userphoneNo');
	} else if (userphoneNo.length != 11) {
		errorTips('请输入11位有效手机号!', 'msg', 'userphoneNo');
	} /*else if (!phoneReg.test(userphoneNo)) {
		errorTips('手机号必须以13,14,15,17,18开头!', 'msg', 'userphoneNo');
	}*/ else {
		return true;
	}
}

function useremail() {
	//var emailReg = /^[a-z]|[0-9]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/;
	//var newReg = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var useremail = $.trim($("#useremail").val());
	if (useremail == '') {
		errorTips('请输入邮箱地址!', 'msg', 'useremail');
	} else if (!new RegExp("^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$").test(useremail)) {
		errorTips('邮箱格式错误!', 'msg', 'useremail');
	} else {
		return true;
	}
}

function userInviterId() {
	var userInviterId = $.trim($("#userInviterId").val());
	var is_invite = $.trim($("#is_invite").val());
	var last_char = userInviterId.substr(-1, 1).charCodeAt();
	if (userInviterId == '') {
		errorTips('请输入邀请码!', 'msg', 'userInviterId');
	} else if ((userInviterId.length < 4 || userInviterId.length > 8) && userInviterId != "0") {
		errorTips('请输入非零开首的4-8位有效邀请码!', 'msg', 'userInviterId');
	} else if (is_invite == "FALSE" && last_char > 65) {
		errorTips('请输入有效的邀请码!', 'msg', 'userInviterId');
	} /*else if (!new RegExp("^[1-9]{3, 7}$").test(userInviterId)) {
	 errorTips('请输入有效的数字账号!', 'msg', 'userInviterId');
	 }*/ else {
		return true;
	}
}

function userverfcode() {
	var userverfcode = $.trim($("#userverfcode").val());
	if (userverfcode == '') {
		errorTips('请输入验证码!', 'msg', 'userverfcode');
	} else {
		return true;
	}
}

function checkphotoBankCard() {
	var bankimg = $("#bankimg").val(); //正面照

	if (bankimg == "") {
		errorTips("请上传银行卡照片!", "msg", "bankimg");
	} else {
		return true;
	}
}

function checkbankclass() {
	var bankclass = $("#bankclass").val(); //开户银行

	if (bankclass == "") {
		errorTips("请输入开户银行!", "msg", "bankclass");
	} else {
		return true;
	}
}

function checkbankNo() {
	var bankno = $.trim($("#bankno").val()); //正面照

	if (bankno == "") {
		errorTips("请输入银行卡号!", "msg", "bankno");
	} else if(bankno.length < 16 || bankno.length > 19) {
		errorTips("请输入大于等于16且小于等于19位的银行卡号!", "msg", "bankno");
	} else {
        return true;
	}
}

function checkbankinfo() {
	var bankinfo = $("#bankinfo").val(); //正面照

	if (bankinfo == "") {
		errorTips("请输入开户支行详细地址!", "msg", "bankinfo");
	} else {
		return true;
	}
}

function checkheadimg() {
	var headimg = $("#headimg").val(); //正面照

	if (headimg == "") {
		errorTips("请上用户头像!", "msg", "headimg");
	} else {
		return true;
	}
}

/*后台添加、修改 账户 共同验证*/
function check_user_type() {
	var usertype = $("#usertype option:selected").val(); //账户模式 0， 1
	var usercycle = $("#usercycle option:selected").val(); //结算周期
	var userrights = $("#userrights").val(); //权益值
	if (usertype == "") {
		errorTips("请选择账户模式!", "msg", "usertype");
	} else {
		if (usertype == "1") {
			if (usercycle == "") {
				errorTips("请选择结算周期!", "msg", "usercycle");
			} else if (userrights == "") {
				errorTips("请输入权益值!", "msg", "userrights");
			} else if (userrights == 0) {
				errorTips("权益值必须大于0!", "msg", "userrights");
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
}

function check_user_rebate() {
	if ($("#userrebate").val() == "") {
		errorTips("请输入返佣比例!", "msg", "userrebate");
	} else if ($("#userrebate").val() < 50) {
		errorTips("返佣比例不能低于50!", "msg", "userrebate");
	} else {
		return true;
	}
}

function check_user_grp() {
	if ($("#usergrpId option:selected").val() == "") {
		errorTips("请选择用户组别!", "msg", "usergrpId");
	} else {
		return true;
	}
}

function check_user_agtId() {
	if ($("#useragtId option:selected").val() == "") {
		errorTips("请选择代理级别!", "msg", "useragtId");
	} else {
		return true;
	}
}

function check_cust_lvg() {
	var cust_lvg = $.trim($("#cust_lvg").val());
	if (cust_lvg == "") {
		errorTips('请输入交易杠杆!', 'msg', 'cust_lvg');
	} else {
		return true;
	}
}

function check_userparentId() {
	var userparentId = $.trim($("#userparentId").val());
	if (userparentId == '') {
		errorTips('请输入上级代理编号!', 'msg', 'userparentId');
	} else if (userparentId != "" && userparentId == 0) {
		return true;
	} else if (userparentId.length < 4 || userparentId.length > 8) {
		errorTips('请输入非零开首的4-8位有效账号!', 'msg', 'userparentId');
	} else {
		return true;
	}
}

function check_settlement_model() {
		if ($("#settlement_model option:selected").val() == "") {
			errorTips("请选择结算模式!", "msg", "settlement_model");
		} else {
			return true;
		}
	}

function init_select_status() {
	if ($("#usertype option:selected").val() == "0") {
		//返佣模式，结算周期和权益值禁止选择和输入
		$("#userrights").css({
			"border": "1px solid #ccc",
			"background": "#E6E6FA !important",
			"color": "#0066ff",
			"cursor":"text",
		}).attr('readonly', 'true');
		$("#select_enabled").css({"display": "none"});
		$("#select_disabled").css({"display": "block"});
	} else if ($("#usertype option:selected").val() == "1") {
		$("#userrights").removeAttr('style').removeAttr("readonly").css({"width": "200px"});
		$("#select_enabled").removeAttr("style").css({"width": "200px", "display": "block", "margin-right": "0px"});
		$("#select_disabled").css({"display": "none", "margin-right": "0px"});
	}
}

//编辑代理商信息
function agentsSave(token, callUrl) {
	console.log(getFormData("AdminAgentsListForm"));
	var index1 = openLoadShade();
	var fromData = getFormData("AdminAgentsListForm");
	$.ajax({
		url: route_prefix() + "/agents/agents_edit_save",
		data: {
			data:                   fromData,
			enable:                 ($("#enable").is(":checked")) ? 1 : 0,
			enablereadonly:         ($("#enable_readonly").is(":checked")) ? 1 : 0,
			isoutmoney:             ($("#is_out_money").is(":checked")) ? 1 : 0,
            settlementmodel:        $("input[type='radio']:checked").val(),
			datausercycle:			$("#usercycle option:selected").val(),
			usergrpName:            $("#usergrpId option:selected").text(),
			useragtName:            $("#useragtId option:selected").text(),
			_token:					token,
		},
		dateType: "JSON",
		type: "POST",
		async: false,
		success: function(data) {
			if (data.msg == "FAIL") {
				closeLoadShade(index1);
				if (data.err == "Existidcard") {
					errorTips("身份证已存在!", "msg", data.col);
				} else if (data.err == "Existphone") {
					errorTips("手机号已存在!", "msg", data.col);
				} else if (data.err == "directExistOrder") {
					errorTips("当前代理商直属客户有持仓单,无法变更结算模式!", "msg", data.col);
				} else if (data.err == "Existemail") {
					errorTips("邮箱已存在!", "msg", data.col);
				} else if (data.err == "INVALIDCYCLE") {
					errorTips("无效的周期结算值!", "msg", data.col);
				} else if (data.err.grp == "err_grp") {
					errorTips("不存在的用户组!", "msg", "usergrpId");
				} else if (data.err.pid == "err_pid") {
					errorTips("无效的上级代理!", "msg", "userparentId");
				} else if (data.err == "ThanAndEqualInviter") {
					errorTips("代理级别不能大于等于邀请人代理商级别!", "msg", data.col);
				} else if (data.err.gid == "err_gid") {
					errorTips("不存在的代理级别!", "msg", "useragtId");
				} else if (data.err.comm_prop == "err_comm_prop") {
					var err_tips = "返佣比例可调范围是: " + data.err.min + "--" + data.err.max;
					errorTips(err_tips, "msg", "userrebate");
				} else if (data.err.act == "EXISTACCOUNT") {
					errorTips("当前代理商返佣比例不能低于50!", "msg", "userrebate");
				} else if (data.err == "ACCOUNTEXISTORDER") {
					errorTips("当前客户有持仓单,无法更改用户组!", "msg", data.col);
				} else if (data.err == "ENABLEUPDFAIL") {
					errorTips("更新账户启用状态失败!", "msg", data.col);
				} else if (data.err == "NETERR") {
					console.log(data.col);
					errorTips("网络故障,请稍后重试或联系技术人员!", "msg", data.col);
				} else if (data.err == "PSWUPDFAIL") {
					errorTips("网络故障,同步更新密码失败,请稍后重试!", "msg", data.col);
				} else if (data.err == "MT4OHTERUPDFAIL") {
					errorTips("网络故障,同步更新数据失败,请稍后重试!", "msg");
				} else if (data.err == "NETERRUPDFAIL") {
					console.log(data.col);
					errorTips("网络故障,请联系技术人员!", "msg", data.col);
				} else if (data.err == "INFOUPDATEFAIL") {
					console.log(data.col);
					errorTips("更新客户信息失败!", "msg", data.col);
				}
			} else if (data.msg == "SUC") {
				layer.msg("更新成功", {
					time: 20000, //20s后自动关闭
					btn: ['知道了'],
					yes: function (index, layero) {
						parent.layer.closeAll();
						window.location.href = callUrl;
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

//编辑客户信息
function custSave(token, callUrl) {
	console.log(getFormData("AdminCustListForm"));
	var index1 = openLoadShade();
	var fromData = getFormData("AdminCustListForm");

	$.ajax({
		url: route_prefix() + "/cust/cust_save_info",
		data: {
			data:                   fromData,
			enable:                 ($("#enable").is(":checked")) ? 1 : 0,
			enablereadonly:         ($("#enable_readonly").is(":checked")) ? 1 : 0,
			isoutmoney:             ($("#is_out_money").is(":checked")) ? 1 : 0,
			usergrpName:            $("#usergrpId option:selected").text(),
			useragtName:            $("#useragtId option:selected").text(),
			_token:					token,
		},
		dateType: "JSON",
		type: "POST",
		async: false,
		success: function(data) {
			if (data.msg == "FAIL") {
				closeLoadShade(index1);
				if (data.err == "Existidcard") {
					errorTips("身份证已存在!", "msg", data.col);
				} else if (data.err == "Existphone") {
					errorTips("手机号已存在!", "msg", data.col);
				} else if (data.err == "Existemail") {
					errorTips("邮箱已存在!", "msg", data.col);
				} else if (data.err.grp == "err_grp") {
					errorTips("不存在的用户组!", "msg", "usergrpId");
				} else if (data.err.pid == "err_pid") {
					errorTips("无效的上级代理!", "msg", "userparentId");
				} else if (data.err == "ACCOUNTEXISTORDER") {
					errorTips("当前客户有持仓单,无法更改用户组!", "msg", data.col);
				} else if (data.err == "ENABLEUPDFAIL") {
					errorTips("更新账户启用状态失败!", "msg", data.col);
				} else if (data.err == "NETERR") {
					console.log(data.col);
					errorTips("网络故障,请稍后重试或联系技术人员!", "msg", data.col);
				} else if (data.err == "PSWUPDFAIL") {
					errorTips("网络故障,同步更新密码失败,请稍后重试!", "msg", data.col);
				} else if (data.err == "MT4OHTERUPDFAIL") {
					errorTips("网络故障,同步更新数据失败,请稍后重试!", "msg");
				} else if (data.err == "NETERRUPDFAIL") {
					console.log(data.col);
					errorTips("网络故障,请联系技术人员!", "msg", data.col);
				} else if (data.err == "INFOUPDATEFAIL") {
					console.log(data.col);
					errorTips("更新客户信息失败!", "msg", data.col);
				}
			} else if (data.msg == "SUC") {
				layer.msg("更新成功", {
					time: 20000, //20s后自动关闭
					btn: ['知道了'],
					yes: function (index, layero) {
						parent.layer.closeAll();
						window.location.href = callUrl;
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
/*end*/

function getFormData(formId) {
    var c = {};
    var p = {};
    $.each($("#" + formId).find("input,select,textarea"), function (e, i) {
        if (i.name = (i.name || "").replace(/^\s*|\s*&/, ""), i.name) {
            if (/^.*\[\]$/.test(i.name)) {
                var t = i.name.match(/^(.*)\[\]$/g)[0];
                p[t] = 0 | p[t], i.name = i.name.replace(/^(.*)\[\]$/, "$1[" + p[t]+++"]");
            }
            (/^checkbox|radio$/.test(i.type)) && !i.checked || (c[i.name] = i.value);
        }
    });

    return c;
}

function openLoadShade() {
	var index = layer.load(1, {shade: 0.4});
	
	return index;
}

function closeLoadShade(index) {
	layer.close(index);
}

//获取交易订单交易类型
function getCmdTypeName(value) {
	switch (Number(value)) {
		case 0:
			return 'Buy';
			break;
		case 1:
			return 'Sell';
			break;
		case 2:
			return 'Buy Limit';
			break;
		case 3:
			return 'Sell Limit';
			break;
		case 4:
			return 'Buy Stop';
			break;
		case 5:
			return 'Sell Stop';
			break;
		case 6:
			return 'Balance';
			break;
		case 7:
			return 'Credit';
			break;
		default:
			return 'Other';
	}
}

//获取用户充值类型
function getAccountdepositType(value) {
	var depositType;
	if (value == null) {
		return '<font color="#FF7F50">未知来源</font>';
	} else if (value == '') {
		return '';
	} else {
		if (value.indexOf('-ZH') > 0) {
			depositType = "佣金转户";
		} else if (value.indexOf('-CZ') > 0) {
			depositType = "账户充值";
		} else if (value.indexOf('-FY') > 0) {
			depositType = "账户返佣";
		} else if (value.indexOf('-QK') > -1) {
			depositType = "账户取款";
		} else if (value.indexOf('-TH') > 0) {
			depositType = "转户退回";
		} else if (value.indexOf('-RJ') > 0) {
			depositType = "平台";
		} else if (value.indexOf('-XY') > 0) {
			depositType = "平台";
		} else if (value.indexOf('-CJTH') > 0) {
			depositType = "出金退回";
		} else if (value.indexOf('Adj') > -1) {
			depositType = "平台";
		} else {
			depositType = "其他";
		}

		return depositType;
	}
}

//获取用户出金状态
function getDrawApplyStatus(value) {
	var str = '';

	if(value == "0") {
		str = "<span style='color:#008B8B;'>待审核</span>"
	} else if(value == "1") {
		str = "<span style='color:#00E5EE;'>正在审核</span>"
	} else if(value == "2") {
		str = "<span style='color:#00EE00;'>已出款</span>"
	} else if(value == "3") {
		str = "<span style='color:#D2691E;'>出款失败</span>"
	} else {
        str = "<span style='color:red;'>未知状态</span>"
	}

	return str;
}

//获取代理级别
function getAgentsLevel(value) {
	var level = "";

	if (value != "") {
		if (value == 1) {
			level = "<span style='color:purple;'>一级</span>";
		} else if (value == 2) {
			level = "<span style='color:blueviolet;'>二级</span>";
		} else if (value == 3) {
			level = "<span style='color:mediumslateblue;'>三级</span>";
		} else if (value == 4) {
			level = "<span style='color:saddlebrown;'>四级</span>";
		}
	}

	return level;
}

//获取代理商结算模式
function getAgentssettlementmodel(value, username) {
	var str_model = "";
	if (username != "") {
		if (value == "1") {
			str_model = "<span style='color:#00ae9d;'>线上</span>";
		} else if (value == "2") {
			str_model = "<span style='color:#f15a22;'>线下</span>";
		} else {
			str_model = "<span style='color:red;'>未知</span>";
		}
	}
	
	return str_model;
}

//判定是否确认代理级别
function IsconfirmLevel(value) {
	var Is_level = "";

	if (value != "") {
		if (value == 1) {
			Is_level = "<span style='background: #7cc33c; color: #fff; border-radius: 13%; font-size: 12px;'>已确认</span>";
		} else {
			Is_level = "<span style='background: #ccc; color: #fff; border-radius: 13%; font-size: 12px;'>未确认</span>";
		}
	}

	return Is_level;
}

function bankNoFormat(value) {
    var str_start_bak = "", str_end_bak = "", str_last = "";
	if (value != '') {
        str_start_bak = value.substring(0,4);
        str_end_bak = value.substring(-4, 0);
        str_end_bak = value.substr(value.length-4);
        str_last = str_start_bak + "****" + str_end_bak;
	}

	return str_last;

}

//获取用户组名
function getUserGroupName(value, rowData) {
	var select = "<select id='groudId"+value+"' name='groudId"+value+"'>";

	//for (var i= Number(value); i <= 4; i ++) {
		//if (i == rowData.userGroupId) {
            select += "<option value='"+ rowData.userGroupId +"' selected=''>" + groupName(rowData.userGroupId) + "</option>";
	//	} else {
            //select += "<option value='"+ i +"'>" + groupName(i) + "</option>";
	//	}
	//}
	
	return select += "</select>";
}

/*获取用户出金申请订单状态*/
function getWithdrawApplyStatus(value) {
	switch (value) {
		case "0":
			return "<span style='color: #00DD00;'>待处理</span>";
		break;
		case "1":
			return "<span style='color: #0066FF;'>正在处理</span>";
		break;
			case "2":
		return "<span style='color: #4B0082 ;'>已处理</span>";
		break;
		case "3":
			return "<span style='color: #FF0000;'>处理失败</span>";
		break;
		default:
			return "<span style='color: #888888;'>未知状态</span>";
	}
}

function groupName(val) {
	switch (Number(val)) {
		case 1:
			return '一级代理';
			break;
		case 2:
			return '二级代理';
			break;
		case 3:
			return '三级代理';
			break;
		case 4:
			return '四级代理';
			break;
		case 5:
			return '普通用户';
			break;
		default:
			return '未知';
	}
}

//重新整理返佣订单，将其订单号设为可以点击并查看订单详情的<a>标签
function trades_detail(value) {
	var start, end, sts, lts, orderNo, html, last;
	if (value.indexOf("#") > 0) {
		start =  value.substring(0,value.indexOf("#") + 1);
		end  = value.substring(value.lastIndexOf("-"));
		sts = value.indexOf("#");
		lts = value.lastIndexOf("-")
		orderNo = value.substring(sts + 1, lts);
		
		return orderNo;
	} else {
		return value;
	}
}

//格式化处理Email
function userEmailFormat(value) {
	var iden = value.indexOf("@");
	var start = value.substring(0, iden);
	var end = value.substring(iden);

	var prefix = start.substring(0, 3);

	return prefix + "*****" + end;
}

//格式化处理Phone
function userPhoneFormat(value) {
	var reprefix = value.substring((value.indexOf("-") + 1));
	var start = reprefix.substring(0, 3);
	var end  = reprefix.substr(reprefix.length - 3);

	return start + "*****" + end;
}

//ajax 获取人物关系，并生成HTML
function getUserRelationShip(uid, role, fname, _token) {
    $.ajax({
        url: "/user/relationShipHtml",
        data: {
            userId: uid,
            role: role,
            fname: fname,
            _token: _token,
        },
        dateType: "JSON",
        type: "POST",
        async: false,
        success: function (data) {
            $("#real").html("");
            $("#real").html("我的位置: ");
            $("#real").append(data.real);
        },
        error: function () {
            layer.msg("未知错误,请尝试重新操作或联系客服.");
        }
    });
}

function maxWindow(index) {
	//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
	$(window).resize(function(){
		layer.full(index);
	});
	layer.full(index);
}

//订单ID, 订单类型, 登录人角色
function orderIdDetail(value, orderType, role) {
	var url = "";
	if (orderType == 'closeOrder') {
		url = '/close/order_detail/';
	} else if (orderType == 'openOrder') {
		url = '/open/order_detail/';
	}
	
	var index = layer.open({
		type: 2,
		title: '订单 ' + value +' 详情',
		skin: 'layui-layer-molv',
		move:false,
		content: url + value +'/'+ orderType +'/'+ role,
	});
	
	maxWindow(index);
}

//查看出金订单详情，并进行相应的操作
function withdrawOrderIdDetail(orderId, ticket) {
    var index = layer.open({
        type: 2,
        title: '出金订单 ' + ticket +' 的详情',
        skin: 'layui-layer-molv',
        area: ['1020px', '550px'],
        move:false,
        content: route_prefix() + "/amount/orderId_detail/" + orderId,
    });
}

//用户ID, 登录人角色
function userIdDetail(value, role) {
	
	var index = layer.open({
		type: 2,
		title: value +' 的账户详情',
		skin: 'layui-layer-molv',
		move:false,
		content: '/show/user_detail/'+ value +'/'+ role,
	});
	
	maxWindow(index);
}

//代理商查看自己直属下级代理商客户信息
function show_direct_cust_info(uid, role) {
	var index = layer.open({
		type: 2,
		title: '查看 ' + uid +' 的账户详情',
		skin: 'layui-layer-molv',
		move:false,
		content: '/user/cust/show_direct_cust_info/'+ role + '/' + uid,
	});
	
	maxWindow(index);
}

//显示返佣订单明细
function show_rebate_order_detail(orderNo, role) {
	var index = layer.open({
		type: 2,
		title: '订单 ' + orderNo +' 详情',
		skin: 'layui-layer-molv',
		move:false,
		content: '/user/realtime/rebate_detail/'+ orderNo + '/' + role,
	});
	
	maxWindow(index);
}

//查看某个代理商直属客户明细
function show_proxy_direct_cust_detail(puid) {
	var index = layer.open({
		type: 2,
		title: puid + ' 的直属客户信息',
		skin: 'layui-layer-molv',
		move:false,
		content: '/user/proxy/direct_cust_detail/' + puid,
	});
	
	maxWindow(index);
}

function agents_edit_info(uid) {
    var index = layer.open({
        type: 2,
        title: '更改代理商' + uid + '的信息',
        skin: 'layui-layer-molv',
        move:false,
        area: ['1050px', '600px'],
        content: route_prefix() + '/agents/agents_edit_info/' + uid,
    });

    //maxWindow(index);
}

//直属客户组别变更
function changeDirectCustGroupInfo(uid) {
	var index = layer.open({
		type: 2,
		title: '变更' + uid +' 交易手续费',
		skin: 'layui-layer-molv',
		move:false,
		content: '/user/cust/change/group/'+ uid,
	});
	
	maxWindow(index);
}

//直属客户（代理商和普通客户）佣金转户
function directProxyCustomerCommissionTransfer(uid) {
	var index = layer.open({
		type: 2,
		title: '佣金转户',
		skin: 'layui-layer-molv',
		move:false,
		content: '/user/proxy/direct_user_commTrans_browse/'+ uid,
	});
	
	maxWindow(index);
}

//User 个人中心
function uploadIdCard() {
	var index = layer.open({
		type: 2,
		title: "上传身份证",
		skin: 'layui-layer-molv',
		move:false,
		area: ['500px', '400px'],
		content: '/user/center/uploadIdCard',
	});
}

function uploadBank() {
	var index = layer.open({
		type: 2,
		title: "上传银行卡",
		skin: 'layui-layer-molv',
		move:false,
		area: ['500px', '430px'],
		content: '/user/center/uploadBank',
	});
}

function uploadBankChange(type) {
	var index = layer.open({
		type: 2,
		title: "银行卡变更",
		skin: 'layui-layer-molv',
		move:false,
		area: ['500px', '640px'],
		content: '/user/center/uploadChangeBank/' + type,
	});
}

function updatePhoneEmail(type) {
	var title = "";
	if (type == "phone") {
		title = "修改手机号码";
	} else if (type == "email") {
		title = "修改邮箱地址";
	}
	var index = layer.open({
		type: 2,
		title: title,
		skin: 'layui-layer-molv',
		move:false,
		area: ['500px', '350px'],
		content: '/user/center/updPhoneEmail/' + type,
	});
}

function accountCancelApply() {
    var index = layer.open({
        type: 2,
        title: "销户申请",
        skin: 'layui-layer-molv',
        move:false,
        area: ['500px', '550px'],
        content: '/user/center/cancelAccount',
    });
}

//查看客户信息
function edit_account_info(acc_uid) {
	var index = layer.open({
        type: 2,
        title: '查看 ' + acc_uid + ' 用户信息',
        skin: 'layui-layer-molv',
        move:false,
	    area: ['1050px', '600px'],
        content: route_prefix() + '/cust/cust_detail/'+ acc_uid,
    });

    //maxWindow(index);
}

//导出Excel
function flow_export(formId, type, role, token) {
	var url = "";
	if (role == "agents") {
		url = "/user/flow/depositExport";
	} else if (role == "admin" && type == "depositFlow") {
		url = route_prefix() + "/amount/depositExport";
	} else if (role == "admin" && type == "RightsSumFlow") {
		url = route_prefix() + "/amount/rightsSumExport";
	} else if (role == "admin" && type == "withdrawApply") {
		url = route_prefix() + "/amount/withdrawApplyExport";
    } else if (role == "admin" && type == "withdrawFlow") {
		url = route_prefix() + "/amount/withdrawFlowExport";
	}
	$.ajax({
		url: url,
		data: {
			data: getFormData(formId),
			type: type,
			role: role,
			_token: token,
		},
		dateType: "JSON",
		type: "POST",
		async: false,
		success: function (data) {
			//console.log(data.msg);
			location.href = data.msg;
		},
		error: function () {
			layer.msg("未知错误,请尝试重新操作或联系客服.");
		}
	});
}

function newsDetail(newsId) {
	var index = layer.open({
		type: 2,
		title: '新闻详情',
		skin: 'layui-layer-molv',
		move:false,
		area: ['700px', '550px'],
		content: '/user/news/news_detail/' + newsId,
	});
}

function encodeHtml(s) {
	var REGX_HTML_ENCODE = /"|&|‘|<|>|[\x00-\x20]|[\x7F-\xFF]|[\u0100-\u2700]/g;
	return (typeof s != "string") ? s :
		s.replace(REGX_HTML_ENCODE,
			function ($0) {
				var c = $0.charCodeAt(0), r = ["&#"];
				c = (c == 0x20) ? 0xA0 : c;
				r.push(c);
				r.push(";");
				return r.join("");
			});
}

function show_certified_detail(uid) {
	var index = layer.open({
		type: 2,
		title: uid + ' 的账户信息',
		skin: 'layui-layer-molv',
		move:false,
		area: ['800px', '550px'],
		content: route_prefix() + '/auth/user_certified_detail/' + uid,
	});
}

function userProfitStyle(value) {
	if (value > 0) {
		return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
	} else {
		return parseFloatToFixed(value);
	}
}

function rightsSumStatus(value) {
	if (value == "1") {
		return "<span style='color: #009688;' data-status="+ value +">"+ '等待结算' +"</span>";
	} else if (value == "2") {
		return "<span style='color: #9900CC;' data-status="+ value +">"+ '已经结算' +"</span>";
	} else {
		return "<span style='color: #CC0000;' data-status="+ value +">"+ '未知状态' +"</span>";
	}
}

function rightsUseCycle(value) {
	if (value =="1") {
		return "<span style='color: #000000;' data-status="+ value +">"+ '周结' +"</span>";
	} else if (value =="2") {
		return "<span style='color: #000099;' data-status="+ value +">"+ '半月结' +"</span>";
	} else if (value =="3") {
		return "<span style='color: #FF3300;' data-status="+ value +">"+ '月结' +"</span>";
	} else {
		return "<span style='color: #990099;' data-status="+ value +">"+ '未知结算周期' +"</span>";
	}
}

function fengXianValFormat(value) {
	var str = "";
	if (Number(value) <= 9) {
		str = "<span style='color: #000000;'>"+ value + "%" +"</span>";
	} else if (10 <= Number(value) <= 30) {
		str = "<span style='color: #0000FF;'>"+ value + "%" +"</span>";
	} else if (31 <= Number(value) <= 50) {
		str = "<span style='color: #FF2D2D;'>"+ value + "%" +"</span>";
	} else if (51 <= Number(value) <= 100) {
		str = "<span style='color: #FF0000;'>"+ value + "%" +"</span>";
	} else if (Number(value) >= 101) {
		str = "<span style='color: #4B0082;'>"+ value + "%" +"</span>";
	}

	return str ;
}

function wezstatus(value)
{
	if (value == "1") {
		return "<span style='color:#6633CC;'>待清零</span>";
	} else if (value == 2) {
		return "<span style='color:#15ccb6;'>已清零</span>";
	} else {
		return "<span style='color:red;'>未知状态</span>";
	}
}

function againSendSMS(uid, _token)
{
	if (!$("#sendSms").hasClass("layui-btn-disabled")) {
		$.ajax({
			url: route_prefix() + "/send/againSendSms",
			data: {
				userId: uid,
				_token: _token,
			},
			dateType: "JSON",
			type: "POST",
			async: false,
			success: function (data) {
				if (data.msg == "SUC") {
					layer.msg('短信发送成功', {icon: 6});
						$("#sendSms").addClass("layui-btn-disabled");
				} else if (data.msg == "FAIL") {
				console.log(data.err);
				layer.msg('短信发送失败', {icon: 5});
				}
			},
			error: function () {
				layer.msg("未知错误,请尝试重新操作或联系客服.");
		}
	});
	}
}
