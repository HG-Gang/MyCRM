<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="{{ URL::asset('css/register.css') }}?ver={{ resource_version_number() }}" rel="stylesheet" type="text/css">
    <script src="{{URL::asset('/js/jquery.min.js')}}?ver={{ resource_version_number() }}"></script>
</head>
<body>
<div style="width:475px; margin: 4px;">
    <div class="layoutbox">
        @if($status == "SUCCESS")
            <h4>恭喜您注册成功，您可以使用此账号登录MT4交易平台</h4>
        @else
            <h4>很抱歉，因网络或其他原因，未能与MT4同步信息</h4>
        @endif
       <ul>
           <li>
               <span>您的交易账户:</span>
               {{--<span style="font-weight: 500; color: #00a0e9;">{{ $rdate['user_id'] }}</span>--}}
               <span style="font-weight: 500; color: #00a0e9;">{{ $rdate->user_id }}</span>
           </li>
           <li>
              <span>您的交易密码:</span>
               @if($source == 'Register')
                    <span style="font-weight: 500; color: #00a0e9;">{{ base64_decode($rdate->password) }}</span>
               @elseif($source == 'LoginIndex')
                    <span style="font-weight: 500; color: #00a0e9;">{{ base64_decode($rdate->password) }}</span>
               @endif
           </li>
       </ul>
        @if($status == "SUCCESS")
            <p id="confirmation" onclick="confirmReg()">确定</p>
        @elseif($status == "FAIL")
            <p id="confirmation" onclick="again_register()">立即同步</p>
        @endif

    </div>
</div>
</body>
</html>
<script type="text/javascript">
    function confirmReg() {
        top.location="/login/index";
    }
	
    function again_register() {
        top.location = "/sync_again_register";
    }
</script>