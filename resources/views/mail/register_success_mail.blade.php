<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册通知</title>
</head>
<style>
    * {
        font-family: 微软雅黑 !important;
        margin: 0;
        padding: 0;
        font-size: 12px;
    }

    .bottom_span {
        color: red;
    }

    #content {
        margin-top: 20px;
        height: 100px;
        text-indent: 3em;
        width: 98%;
        line-height: 30px;

    }
    a{
       color:#6a8aa0;
    }
	a:active {
		color: #fff;
	}
    ul {
        list-style: none;
    }
    p>i>span{
        font-size:25px !important;
    }
        i>a{
            color: #fff !important;
        }

</style>
<body>
<div style="
        width: 100%;
        height: 100%;">
    <div style=" width: 800px;
        height: 100%;
        margin: 0 auto;
        border-top: 3px solid #0068c1;">
        <ul style=" width: 100%;
        height: 70px;  list-style: none;padding:0px;">
            <li style="width: 40%;
        height:100%;
        position: relative;
        float: left;     list-style: none;">

                <img src="{{ url('img/email_logo.png') }}?ver={{ resource_version_number() }}" alt="" style="position: absolute;height: 60px;left: 31px;top: 5px;">
                <p style="font-size:25px;font-weight: 600;margin-top: 6px;text-indent: 4em; line-height: 32px;color:#0068c1;margin-bottom: 0;">
                    帕达控股</p>
                <p style="line-height:20px; text-indent: 6.7em; line-height: 32px;margin: 0;"><a href="http://{{ Official_web_address() }}" target="_blank">{{ Official_web_address() }}</a>
                </p>
            </li>
            {{--<li style="width: 30%;height:100%;position: relative;float: left;    list-style: none;">
                <p style="margin-top: 3px; line-height: 32px;margin-bottom: 0;">新西兰授权许可</p>
                <p style="line-height:20px; margin: 0;line-height: 32px;font-size:13px !important;">编号：FSP562026</p>
            </li>
            <li style=" width: 30%;
        height:100%;
        position: relative;
        float: left;    list-style: none;">
                <p style="font-size:25px!important;font-weight: 600;
               color:#0068c1;line-height: 70px;margin: 0;"><i style="font-size:25px;">00852-54956034</i></p>
            </li>--}}
        </ul>

        <div style=" width: 100%;
        height: 500px;
        background: #edeff7;
        border-top: 3px solid #0068c1;">
            <p style="text-indent: 2em;font-weight: 600;  line-height: 35px;margin: 0;
        text-indent: 4em;
        font-size: 13px;margin-top:30px;">尊敬的 <span style=" color:#0068c1;padding: 0 10px;border-bottom: 1px solid #0068c1;font-size:14px;">{{ $user->user_name }}</span>@if($user->sex == '男')先生@elseif($user->sex == '女')小姐@endif
            </p>
            <p style="line-height: 35px;
        text-indent: 5em;margin: 0;
        font-size: 13px;">您好，您于 <strong>{{ $user->rec_crt_date }} </strong>在帕达控股官网开通的账户申请已审核通过，您的账户已经成功开通。 </p>
            <p style="  text-indent: 3em;  line-height: 35px;margin: 0;
        text-indent: 5em;
        font-size: 13px;">你的交易账户是：<span
                    style=" color: #0068c1;padding: 0 10px;border-bottom: 1px solid #0068c1;">{{ $user->user_id }}</span></p>
            @if($send_type == 'verifyemail')
                <p style="margin-bottom: 30px;  text-indent: 3em;  line-height: 35px;text-indent: 5em;ont-size: 13px;">你的交易密码是：<span style=" color: #0068c1;padding: 0 10px;border-bottom: 1px solid #0068c1;">{{ base64_decode($user->password) }}</span>
            @endif

            <p style="  line-height: 35px;margin: 0;
        text-indent: 4em;
        font-size: 13px;"><a style="display: inline-block; width: 120px;margin-top: -4px;"
                             href="{{ MT4_download() }}" target="_blank">点击此处</a>立即下载帕达控股MT4交易平台</p>
            <p style="  line-height: 35px;margin: 0;
        text-indent: 4em;
        font-size: 13px;"><a style=" display: inline-block;width: 120px; margin-top: -4px;"
                             href="{{ URL::asset('/') }}" target="_blank">点击此处</a>登录用户中心进行存款交易</p>
            {{--<p style="  line-height: 35px;
        text-indent: 4em;
        font-size: 13px;"><a style=" display: inline-block;width: 120px;margin-top: -4px;" href="javascript:;" onclick="openChat('https://chat7.livechatvalue.com/chat/chatClient/chatbox.jsp?companyID=900939&configID=63887&jid=3324094179&s=1',900,600);" target="_blank">点击此处</a>咨询帕达控股24小时在线客服
            </p>

            <p style="  margin-top: 30px;  line-height: 35px;
        text-indent: 4em;
        font-size: 13px;">
                若有任何疑问，欢迎随时咨询24小时在线客服。
            </p>--}}
            <p style=" padding-bottom: 40px;  line-height: 35px;
        text-indent: 4em;
        font-size: 13px;">
                感谢您对帕达控股的支持，我们将为您提供一个安全，稳定，省心的投资平台。
            </p>
        </div>
        <div style=" width: 100%;
        height: 100px;
        background:rgb(52,54,66);">
            <ul style=" width: 800px;
        height: 100%;
        margin: 0 auto;  list-style: none;">
                <li style="width: 40%;
        float: left;
        box-sizing: border-box;
        height: 40px;
        line-height:40px;
        text-indent: 2em;
        color: #fff;    list-style: none;">
                    {{--<p  style="height: 40px;line-height:40px;margin: 0;">客服：xxxxx</p>
                    <p  style="height: 40px;line-height:40px; color:#fff;margin: 0;">邮箱：<a style="color:#fff;">cs@mfg-fx.com</a></p>--}}

                </li>
                <li style="width: 60%;
        float: left;
        box-sizing: border-box;
        height: 40px;
        line-height:40px;
        text-indent: 2em;
        color: #fff;    list-style: none;">
                    <p  style="height:40px;line-height:40px;margin: 0;">工作时间：周一到周五 09:00-17:00</p>
                    <p  style="height: 40px;line-height:40px;margin: 0;">公司地址：Flat/Rm 7022, Blk D 7/F, Tak Wing Ind Bldg, 3 Tsun Wen Road Tuen Mun Nt, Hong Kong</p>
                </li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>