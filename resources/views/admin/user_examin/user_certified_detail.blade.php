@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
    <form class="layui-form" action="" id="AdminCertifiedDetailForm" style="margin-top: 8px;">
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
                    <input type="text" id="useremail" name="useremail" value="{{ substr_replace($_info['email'], '*****', 3, (stripos($_info['email'], '@') - 3)) }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">身份证号码</label>
                <div class="layui-input-block">
                    <input type="text" id="userIdcardNo" name="userIdcardNo" value="{{ $_info['IDcard_no'] }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">银行卡号</label>
                <div class="layui-input-block">
                    <input type="text" id="userIdcardNo" name="userbankNo" value="{{ $_info['bank_no'] }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">开户银行</label>
                <div class="layui-input-block">
                    <input type="text" id="bank_class" name="bank_class" value="{{ $_info['bank_class'] }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">支行地址</label>
                <div class="layui-input-block">
                    <input type="text" id="bank_info" name="bank_info" value="{{ $_info['bank_info'] }}" autocomplete="off" class="layui-input" readonly="readonly" style="width: 200px; border: 1px solid #ccc; background: #E6E6FA !important; color: #0066ff; cursor:text;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layer-photos-demo" style="margin-left: 30px;">
                <a href="{{ URL::asset($_info['img'][0]['img_idcard01_path']) }}" target="_blank" alt="点击查看大图">
                    <img src="{{ URL::asset($_info['img'][0]['img_idcard01_path']) }}" alt="身份证正面照" style="width: 100px; height: 100px;">
                </a>
                <a href="{{ URL::asset($_info['img'][0]['img_idcard02_path']) }}" target="_blank" alt="点击查看大图">
                    <img src="{{ URL::asset($_info['img'][0]['img_idcard02_path']) }}" alt="身份证反面照" style="width: 100px; height: 100px;">
                </a>
                <a href="{{ URL::asset($_info['img'][0]['img_bank_path']) }}" target="_blank" alt="点击查看大图">
                    <img src="{{ URL::asset($_info['img'][0]['img_bank_path']) }}" alt="银行卡" style="width: 100px; height: 100px;">
                </a>
            </div>
        </div>
    </form>
@endsection

@section('custom-resources')

@endsection