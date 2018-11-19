<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>个人资料</title>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="format-detection" content="telephone=no">
        <link rel="stylesheet" href="{{asset('admin/as/layui/css/layui.css')}}" media="all" />
        <link rel="stylesheet" href="{{asset('admin/as/css/user.css')}}" media="all" />
    </head>
    <body class="childrenBody">
        <form class="layui-form">
            <div class="user_left">
                <div class="layui-form-item">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-block">
                        <input type="text" value="{{$admin->username}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">用户组</label>
                    <div class="layui-input-block">
                        <input type="text" value="{{RoleName($admin->role_id)}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>           
                <div class="layui-form-item">
                    <label class="layui-form-label">手机号码</label>
                    <div class="layui-input-block">
                        <input type="tel" value="" placeholder="请输入手机号码" lay-verify="required|phone" class="layui-input" name="mobile" id="mobile">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">邮箱</label>
                    <div class="layui-input-block">
                        <input type="text" value="" placeholder="请输入邮箱" lay-verify="required|email" class="layui-input" name="email" id="email">
                    </div>
                </div>
            </div>
         
            <div class="layui-form-item" style="margin-left: 5%;">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit="" lay-filter="changeUser" type="button" id="but">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
      
        <script type="text/javascript" src="{{asset('admin/as/js/jquery-2.1.1.min.js')}}"></script>
          <script type="text/javascript" src="{{asset('admin/layer/layer.js')}}"></script>
        <!--<script type="text/javascript" src="{{asset('admin/as/js/user.js')}}"></script>-->
        <script>
         $(function(){
             $('#but').click(function(){
              var mobile=$('#mobile').val();
              var email=$('#email').val();
            if(mobile==""){
                   layer.msg('手机号不能为空', {icon: 2, time: 2000});
                   return false;
            }
             if(email==""){
                   layer.msg('邮箱不能为空', {icon: 2, time: 2000});
                    return false;
            }        
           $.ajax({
               url:"{{url(route_prefix() . '/userinfo/save')}}",
               type:'post',
               data:{
                   'mobile':mobile,
                   'email':email,
                   '_token': '{{csrf_token()}}'
               },
               dataType:"json",
               success:function(d){
                   if(d.state==1){
                        layer.msg(d.msg, {icon: 1, time: 2000}); 
                   }else{
                        layer.msg(d.msg, {icon: 2, time: 2000}); 
                   }
               }
               
               
               
               
           });
              
              
              
              
             });
         })
            
            
            
            
        </script>
        
        
        
        
    </body>
</html>