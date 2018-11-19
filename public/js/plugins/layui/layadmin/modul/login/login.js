layui.config({base: '../js/plugins/layui/layadmin/modul/common/'}).use(['form', 'dialog', 'element'],function(){
    var form = layui.form,
        $ = layui.jquery,
        dialog = layui.dialog;

    // 验证码刷新
    $("#refreshcptcode").click(function(){
	    var imgObj = document.getElementById("refreshcptcode");
	    imgObj.src = 'captcha?' + Math.random();
         
      //  $(this).attr('src', 'captcha');
    });
	
	/**
	 * 输入完验证码后,回车后直接完成登录操作
	 */
	/*$('#cptcode').on('keydown', function (e) {
		e.keyCode == 13 && form.on("submit(login)", function (data) {
			alert('2');
		});
	});*/
	
    //登录按钮事件
    form.on("submit(login)",function(data){
        $.ajax({
            url: 'signIn',
	        type: 'POST',
	        dataType: 'JSON',
	        async:false,
            data: data.field,
            error: function (data) {
	            dialog.msg('网络故障,请稍后再登录.');
            },
            success: function (data) {
                re_captcha();
                if (data.loginStatus == 200) {
                    dialog.msg('登录成功,正在为您跳转...请稍等');
                    setTimeout(function () {
                        top.location.href='index';
                    }, 3000);
                } else if (data.loginStatus == 1000) {
	                console.log(data.notsyncmt4);
	                dialog.msg('在线同步注册MT4成功,正在为您跳转...请稍等');
	                setTimeout(function () {
		                top.location.href='index';
	                }, 3000);
                } else if (data.loginStatus == 0) {
                    dialog.msg(data.notactive);
                    return;
                } else if (data.loginStatus == 404) {
                    dialog.msg(data.errpsw);
                    return;
                } else if (data.loginStatus == 400) {
                    dialog.msg(data.errcptcode);
                    return;
                } else if (data.loginStatus == 500) {
	                dialog.msg(data.mt4msg);
	                return;
                } else if (data.loginStatus == 1001) {
	                dialog.msg(data.notsyncmt4);
	                return;
                }
            }
        });
        return false;
    });
    
    function re_captcha () {
		var imgObj = document.getElementById("refreshcptcode");
		imgObj.src = 'captcha?' + Math.random();
		//$("#refreshcptcode").attr('src', 'user/captcha');
		//$('[name="loginUid"]').val('');
		//$('[name="loginPassword"]').val('');
	}
});