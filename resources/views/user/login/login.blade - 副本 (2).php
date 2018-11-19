<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="img/favicon.html">
    <title>客户中心|登录</title>
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap/bootstrap-reset.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/font-awesome/font-awesome.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap/style.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap/style-responsive.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/layui/css/layui.css') }}" media="all" />
</head>
<style>
    body{
        overflow: hidden;
    }
    .baxxin{
        display: inline-block;
        position:absolute;
        left: 54%;
        top: 25%;
        margin-left: -17%;
        width: 50%;
        opacity: 0.8;
    }
    .form-group {
        margin-bottom: 15px;
        width: 50%;
        margin-left: 95px;
    }
    .form-control {
        border: 1px solid #e2e2e4;
        box-shadow: none;
        color: #000 !important;
    }
</style>
<body class="login-body">
<img src="{{ URL::asset('img/bglogin.jpg') }}" alt="" style="width: 100%;height:100%;max-width: 100%; min-height: 1000px">
<section>
    <section class="baxxin">
        <!-- page start-->
        
        <div class="row">
            <div class="" style="width: 500px;">
                <section class="panel" style="box-shadow: 9px 9px 2px -4px #888;">
                    <header class="panel-heading">
                        <p style="text-align: center">
                            <img src="{{ URL::asset('img/logo/login_logo.png') }}" alt=""><span style="color:#164398;font-size: 25px;position: relative;
    top: 3px;"> | 客户中心</span></p>
                    </header>
                    <div class="panel-body">
                        <form id="loginForm" role="form">
                            <div class="form-group " style="position: relative;">
                                <label for="loginUid">账户号码</label>
                                <input type="text" class="form-control " id="loginUid" name="loginUid" placeholder="请输入账号" style="width: 300px;">
                                <i class=" icon-user" style="position: absolute;right: -57px;top: 28px;font-size: 25px;"></i>
                            </div>
                            <div class="form-group" style="position: relative;">
                                <label for="loginPassword">密码</label>
                                <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="123456" style="width: 300px;">
                                <i class=" icon-key" style="position: absolute;right: -57px;top: 28px;font-size: 25px;"></i>
                            </div>
                            <div class="form-group" style="position: relative;">
                                <label for="cptcode" >验证码</label>
                                <input type="text" id="cptcode" name="cptcode" class="form-control"  placeholder="请输入验证码" style="width: 150px;">
                                <a onclick="javascript:re_captcha();" >
                                    <img id="refreshcptcode" src="{{ url ('user/captcha') }}" alt="captcha" style="position: absolute;left:150px;top: 23px;">
                                </a>
                            </div>
                            <div class="form-group" style="margin-top: 35px; width: 53%;">
                                <button class="btn btn-login" style="padding: 6px 35px;background: #d5af43;border-color: #d5af43;" onclick="loginSignIn();">登录</button>
                               {{-- <button type="submit" class="btn btn-info" style=" float: right;   padding: 6px 35px; background: #d5af43;border-color: #d5af43; ">重置</button>--}}
                            </div>
                        </form>
                    
                    </div>
                </section>
            </div>
        
        </div>
        
        
        <!-- page end-->
    </section>
</section>


<section class="" style="position: fixed;width: 100%;bottom:5%;text-align: center">
    <div class="panel-body">
        <a class="btn  " data-toggle="modal" href="#myModal">
            免责声明 |
        </a>
        <a class="btn " data-toggle="modal" href="#myModal2">
            风险披露 |
        </a>
        <a class="btn " data-toggle="modal" href="#myModal3">
            联系我们
        </a>
        <p style="margin-top: 20px;">Copyright © {{ date ('Y') }} 帕达控股版权所有，不得转载</p>
        <!-- Modal -->
    
    
    </div>
</section>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:b;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Modal Tittle</h4>
            </div>
            <div class="modal-body">
                
                Body goes here...
            
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-success" type="button">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- modal -->
<!-- Modal -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Modal Tittle</h4>
            </div>
            <div class="modal-body">
                
                Body goes here...
            
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-warning" type="button"> Confirm</button>
            </div>
        </div>
    </div>
</div>
<!-- modal -->
<!-- Modal -->
<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Modal Tittle</h4>
            </div>
            <div class="modal-body">
                
                Body goes here...
            
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" type="button"> Ok</button>
            </div>
        </div>
    </div>
</div>
<!-- modal -->
<script src="{{ URL::asset('js/jquery-1.11.3/jquery-1.11.3.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/layui/layadmin/layui/layui.all.js') }}"></script>
<script src="{{ URL::asset('js/formevent/formevent.js') }}"></script>
</body>
</html>