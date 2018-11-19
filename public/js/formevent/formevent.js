var isClick = 0;
var aCity={
    11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江",
    31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",
    50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"
};

$(function () { //初始化表单提交按钮的状态
    $("#btn-sub").css({
        "cursor": "not-allowed",
        "color": "#666666",
        "background": "#9199a7",
    });
    $("input:checkbox").click("checkChange",function () {
        if(!$("#agree_pass").is(':checked')) {
            $("#btn-sub").css({
                "cursor": "not-allowed",
                "color": "#666666",
                "background": "#9199a7",
            });
        } else {
            $("#btn-sub").removeAttr("style");
        }
    }).change();
});

$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

/*function re_captcha() {
	var imgObj = document.getElementById("codeimage");
	imgObj.src = 'captcha?' + Math.random();
}*/

function loginSignIn() {
    var layer = layui.layer;
    var form  = $("#loginForm");
    var loginUid        = $.trim($("#loginUid").val());
    var loginPassword   = $.trim($("#loginPassword").val());
	var cptcode         = $.trim($("#cptcode").val());
	if ($.empty(loginUid)) {
		layer.msg("请输入账号号码!");
		return;
    } else if ($.empty(loginPassword)) {
		layer.msg("请输入密码!");
		return;
    } else if ($.empty(cptcode)) {
		layer.msg("请输入验证码!");
		return;
    }
    alert('111');
	var index = layer.load(0, {shade: false});
	console.log(form.serialize());
	/*$.ajax({
		url: 'login/signIn',
		data: {
		    data: form.serialize()
        },
		dateType: "JSON",
		type: "POST",
		async: false,
		success: function (data) {
		},
		error: function (data) {
		
		}
	});*/
}


/**
 * 中文姓名验证
 * @param isSubmit
 * @param type 验证类型，区分提示类型
 * @returns
 */
function checkChineseName(isSubmit, type) {
    var nameReg = /^[\u4E00-\u9FA5]{2,30}$/;
    var user_name = $.trim($("#user_name").val());

    if(user_name != "" && !nameReg.test(user_name)) {
        $("#user_name_error").css("color", "red").html("请输入30个以内的汉字");
        return false;
    } else if(user_name != "" && nameReg.test(user_name)) {
        $("#user_name_error").removeAttr("style").html("");
        return isSubmit;
    } else if(user_name == "" && type == "3") {
        $("#user_name_error").css("color", "red").html("真实姓名为必填选项!");
        return false;
    } else if(type == "1"){
        $("#user_name_error").removeAttr("style").html("");
    }

    return isSubmit;
}

/**
 * 身份证号码验证
 * @param isSubmit
 * @param type 验证类型，区分提示类型
 * @returns
 */
//test 421126199104170038 = 18
//test 421126910417003， 只验证地区和出生日期是否合法
function checkIdcardNo(isSubmit, type) {
    var idcard = $("#IDcard_no").val();
    var isCardReg = /(^\d{15}$)|(^\d{17}(\d|X)$)/;
    var dateReg = /^(19|20)\d{2}-(1[0-2]|0?[1-9])-(0?[1-9]|[1-2][0-9]|3[0-1])$/;
    if( idcard != "" && !isCardReg.test((idcard))) {
        $("#IDcard_no_error").css("color", "red").html("证件号长度必须为15或18位，或字母必须大写！");
        return false;
    } else if( idcard != "" && isCardReg.test((idcard))) {
        $("#IDcard_no_error").removeAttr("style").html("");
        return isSubmit;
    } else if(idcard == "" && type == "3") {
        $("#IDcard_no_error").css("color", "red").html("证件号码为必填选项!");
        return false;
    } else if(type == "3" && isCardReg.test((idcard))) {//这里说明证件号格式及长度都符合要求，现检测出生日期
        var sBirthday=idcard.substr(6,4)+"-"+Number(idcard.substr(10,2))+"-"+Number(idcard.substr(12,2));
        if(aCity[parseInt(idcard.substr(0,2))]==null) {
            $("#IDcard_no_error").css("color", "red").html("身份证号码地区未知,请核对");
            return false;
        } else if(!dateReg.test(sBirthday)) {
            $("#IDcard_no_error").css("color", "red").html("身份证号出生日期非法");
            return false;
        }
    } else if(type == "1"){
        $("#IDcard_no_error").removeAttr("style").html("");
    }

    return isSubmit
}
/**
 * 手机号码验证
 * @param isSubmit
 * @param type 验证类型，区分提示类型
 * @returns
 * todo 目前只验证国内手机号格式
 * 提取信息中的网络链接:(h|H)(r|R)(e|E)(f|F) *= *('|")?(\w|\\|\/|\.)+('|"| *|>)?
 提取信息中的邮件地址:\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*
 提取信息中的图片链接:(s|S)(r|R)(c|C) *= *('|")?(\w|\\|\/|\.)+('|"| *|>)?
 提取信息中的IP地址:(\d+)\.(\d+)\.(\d+)\.(\d+)
 提取信息中的中国电话号码（包括移动和固定电话）:(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}
 提取信息中的中国邮政编码:[1-9]{1}(\d+){5}
 提取信息中的中国身份证号码:\d{18}|\d{15}
 提取信息中的整数：\d+
 提取信息中的浮点数（即小数）：(-?\d*)\.?\d+
 提取信息中的任何数字 ：(-?\d*)(\.\d+)?
 提取信息中的中文字符串：[\u4e00-\u9fa5]*
 提取信息中的双字节字符串 (汉字)：[^\x00-\xff]*
 */
function checkPhoneNo(isSubmit, type) {
    //var phoneReg = /^\d{11}$/;
    var phoneReg = /^1(3|4|5|7|8)\d{9}$/;
    var phone_no = $("#phone").val();

    if(phone_no != "" && phone_no.length != 11 && type == "3") {
        $("#phone_error").css("color", "red").html("请输入11位有效手机号");
        return false;
    } else if(phone_no != "" && !phoneReg.test(phone_no) && type == "3") {
        $("#phone_error").css("color", "red").html("手机号必须以13,14,15,17,18开头");
        return false;
    } else if(phone_no != "" && phoneReg.test(phone_no) && type == "3") {
        $("#phone_error").removeAttr("style").html("");
        return isSubmit;
    } else if(phone_no == "" && type == "3") {
        $("#phone_error").css("color", "red").html("手机号为必填选项!");
        return false;
    } else if(type == "1"){
        $("#phone_error").removeAttr("style").html("");
    }

    return isSubmit
}

/**
 * 电子邮箱验证
 * @param isSubmit
 * @param type 验证类型，区分提示类型
 * @returns
 */
function checkEmail(isSubmit, type) {
    var emailReg2 = /^([\w\-]+\@[\w\-]+\.[\w\-]+)$/;
    var emailReg = /^[a-z]|[0-9]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/;
    var email= $("#email").val();
    if($.trim(email) != "" && !emailReg2.test(email) && type == "3") {
        $("#email_error").css("color", "red").html("邮箱格式错误");
        return false;
    } else if($.trim(email) != "" && emailReg2.test(email) && type == "3") {
        $("#email_error").removeAttr("style").html("");
        return isSubmit;
    } else if(email == "" && type == "3") {
        $("#email_error").css("color", "red").html("邮箱为必填选项!");
        return false;
    } else if(type == "1"){
        $("#email_error").removeAttr("style").html("");
    }

    return isSubmit;
}

/*
 * 介绍人账号验证
 * @param isSubmit
 * @param type 验证类型，区分提示类型
 * @returns
 */
function checkInviterID(isSubmit, type) {
    var PidReg = /^([1-9]{3, 7})(A?)$/; //^(\d{1,7})(A?)$， [1-9]{3,7}A?
    var userIDReg = /^[1-9]{3, 7}$/;
    var parent_id= $("#parent_id").val();
    var last_char = parent_id.substr(-1, 1).charCodeAt();

    if(Number(last_char) < 65 && parent_id.length >= 8 && type == "3") {
        //编号是纯数字
        $("#parent_id_error").css("color", "red").html("无效的介绍人编号");
        return false;
    } else if (parent_id.length < 4 && type == "3") {
        $("#parent_id_error").css("color", "red").html("请输入非零开首的4-8位有效账号");
        return false;
    } else if (Number(last_char) == 65 && parent_id.length <= 8 && type == "3") {
        //编号是纯数字 + 字母 A
        isSubmit = true;
    } else if (Number(last_char) > 65 && type == "3") {
        $("#parent_id_error").css("color", "red").html("无效的介绍人编号!");
        return false;
    }

    if($.trim(parent_id) != "" && userIDReg.test(parent_id) && type == "3") {
        $("#parent_id_error").removeAttr("style").html("");
        return isSubmit;
    } else if($.trim(parent_id) == "" && type == "3") {
        $("#parent_id_error").css("color", "red").html("账号为必填选项!");
        return false;
    } else if(type == "1"){
        $("#parent_id_error").removeAttr("style").html("");
    }

    return isSubmit;
}

/*
 * 发送验证码验证
 * @param isSubmit
 * @param type 验证类型，区分提示类型
 * @returns
 */
function sendVerifCode(type) {
    
    if(isClick == 0) {
    	isClick = 1;
        layui.use(['layer'], function () {
            var layer = layui.layer;
            var isSubmit = true;
            if(checkIdcardNo(isSubmit, type) && checkPhoneNo(isSubmit, type) && checkEmail(isSubmit,type)) {
                var IdcardNo = $.trim($("#IDcard_no").val());
                var email = $.trim($("#email").val());
                var send_type = $("input[name='send_type']:checked").val();
                if(send_type != null) { //todo 发送验证码前检查身份证手机号的唯一性
                    //todo 暂时保留这个逻辑
                    /*var register_type = $("#register_type").val()
                     if(register_type == null && register_type == '') {
                     register_type = 'User';
                     }*/
                    $.get('/check_idcardNo_phone', {
                        IdcardNo: IdcardNo, //todo 检查身份证号唯一性
                        email: email,
                        phone_no_area: $.trim($("#phone_area").val() + "-" + $("#phone").val()), //todo 检查手机号唯一性
                    }, function(data) {
                        if (data.status == "EXISTS") { //todo 检查是否被注册过
                            if(data.col1 == "IDcard_no") {
                                isClick = 0;
                                layer.msg("该身份证号已被注册!");
                                //$("#" + data.col1 + "_error").css("color", "red").html("该身份证号已被注册!");
                            }
                            if(data.col2 == "phone") {
                                isClick = 0;
                                layer.msg("该手机号已经注册!");
                                //$("#" + data.col2 + "_error").css("color", "red").html("该手机号已经注册!");
                            }
                            if(data.col3 == "email") {
                                isClick = 0;
                                layer.msg("该邮箱已经注册!");
                                //$("#" + data.col3 + "_error").css("color", "red").html("该邮箱已经注册!");
                            }
                        } else if (data.status == "OK") { //todo 开始发送验证码程序
                            var stoptime = 0;
                            var countdown = 59;
                            var url = "/register/sendVeriCode";
                            var _this = $("#btn-kh");
                            _this.addClass("get-code-disable");
                            _this.html(countdown + "s后可重取");
                            //启动计时器，1秒执行一次
                            var timer = setInterval(function(){
                                if (countdown == 0) {
                                    isClick = 0;
                                    stoptime = 0;
                                    clearInterval(timer);//停止计时器
                                    _this.removeClass("get-code-disable");//启用按钮
                                    _this.addClass("get-code");
                                    _this.html("获取验证码");
                                }
                                else {
                                    countdown--;
                                    _this.addClass("get-code-disable");
                                    _this.html( countdown + "s后可重取");
                                }
                            }, 1000);
                            if(stoptime == 0) {
                                $.ajax({
                                    headers: {'X-CSRF-TOKEN': $("meta[name='_token']").attr('content')},
                                    url: url,
                                    data: {
                                        IdcardNo: IdcardNo,
                                        phoneNoArea: $.trim($("#phone_area").val() + $("#phone").val()), //手机
                                        phone: $.trim($("#phone").val()),
                                        phone_area: $.trim($("#phone_area").val()),
                                        email: $("#email").val(), //邮箱
                                        send_type: send_type,
                                    },
                                    type: "POST",
                                    dataType: "JSON",
                                    async: false,
                                    success: function(data) {
                                        if(data != null && data.send_info == "SUCCESS") {
                                            layer.tips('发送成功', _this, {
                                                tips: [1, '#3595CC'],
                                                time: 4000
                                            });

                                        } else if(data.send_info == "FAIL") {
                                            layer.tips('发送失败，请检查邮箱或者手机号是否正确', _this, {
                                                tips: [1, '#3595CC'],
                                                time: 4000
                                            });
                                        }
                                    },
                                    error: function(data) {
                                        layer.tips('发送失败，网络错误，请重新操作', _this, {
                                            tips: [1, '#3595CC'],
                                            time: 4000
                                        });
                                    }
                                });
                            }
                            stoptime++;
                        }
                    });
                }
            } else {
                isClick == 0
            }
        });
    }
}

/**
 * 自设密码验证
 * @param isSubmit
 * @param type 验证类型，区分提示类型
 * @returns
 */
function checkPassword(isSubmit,type){
   // var pasReg = /^[a-zA-Z]{8,12}$/;
    var pasReg = /^[A-Za-z]\w{5,}$/;
    var password_1 = $.trim($("#password_1").val());
    if(password_1 != "" && !pasReg.test(password_1)) {
        $("#password_1_error").css("color", "red").html("密码长度最低5位，且开首必须是字母!");
        return false;
    } else if(password_1 != "" && pasReg.test(password_1) && type == "3") {console.log('2');
        $("#password_1_error").removeAttr("style").html("");
        return isSubmit;
    } else if(password_1 == "" && type == "3") {
        $("#password_1_error").css("color", "red").html("密码为必填选项!");
        return false;
    } else if(type == "1"){
        $("#password_1_error").removeAttr("style").html("");
    }

    return isSubmit;
}

/*
 * 确认密码
 * */
function checkAgainPassword(isSubmit,type) {
    var password_2 = $("#password_2").val();
    var password_1 = $("#password_1").val();
    if(password_2 !== "" && checkPassword(true, "3")) {
        if(password_1 !== password_2 && type == "3") {
            $("#password_2_error").css("color", "red").html("两次密码不一样!");
            return false;
        } else {
            $("#password_2_error").removeAttr("style").html("");
            return isSubmit;
        }

    } else if(type == "3" && password_2 == "") {
        $("#password_2_error").css("color", "red").html("请输入确认密码!");
        return false
    } else if(type == "1") {console.log('2');
        $("#password_2_error").removeAttr("style").html("");
    }

    return isSubmit;
}

function registerSuccess(status, source) {
    layui.use(['layer'], function () {
        var layer = layui.layer;
        if(source == "LoginIndex") {
            layer.open({
                title: ["提示信息", "background:#335B9F; color: #fff"],
                type: 2,
                shade: [0.6, '#393D49'],
                area: ['500px', '250px'],
                anim: 0,
                move: false,
                content: ['/registerComment/' + status + '/' + source],
                resize: false,
                closeBtn: 0,
            });
        } else if(source == "Register") {
            layer.open({
                title: ["提示信息", "background:#335B9F; color: #fff"],
                type: 2,
                shade: [0.6, '#393D49'],
                area: ['500px', '250px'],
                anim: 0,
                move: false,
                content: ['/registerComment/' + status + '/' + source],
                resize: false,
                cancel: function(index, layero){
                    window.location="/login/index";
                },
            });
        }
    });
}

