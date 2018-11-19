@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<div>
		<ul>
			<li>
				<em>实名认证</em>
					身份证号码: {{ substr_replace($_info['IDcard_no'], '********', 6, -4) }}
				@if($_info['IDcard_status'] == '0')
					<span>待上传, <a href="javascript:void(0)" onclick="uploaIdCard()" style="color: blue; cursor: pointer;">请上传资料</a></span>
				@elseif($_info['IDcard_status'] == '1')
					<span style="color: blue; cursor: not-allowed;"><i class="layui-icon">&#xe650;</i>正在审核中</span>
				@elseif($_info['IDcard_status'] == "2")
					<span>已实名认证</span>
				@elseif($_info['IDcard_status'] == "4")
					<span><i class="lx-icon" id="name-icon"></i>审核不通过,<a href="javascript:void(0)" onclick="uploaIdCard()" style="color: blue; cursor: pointer;">请重新上传资料</a></span>
				@endif
			</li>
		</ul>
		<li style="position:relative"><em>取款银行卡：</em>银行卡号:
			@if($_info['bank_status']== '2')
				{{ substr_replace($_info['bank_no'], '**********', 4, -4) }}
			@else
				{{ substr_replace($_info['bank_no_tmp'], '**********', 4, -4) }}
			@endif
			@if($_info['bank_status'] == '0')
				<span onclick="BindBankCard()" style="color: blue; cursor: pointer;"><i class="layui-icon">&#xe620;</i>待上传</span>
			@elseif($_info['bank_status'] == '1')
				<span style="color: blue; cursor: not-allowed;"><i class="layui-icon">&#xe650;</i>正在审核中</span>
			@elseif($_info['bank_status'] == '2')
				<span style="color: blue; cursor: not-allowed;"><i class="layui-icon">&#xe650;</i>审核通过</span>
			@elseif($_info['bank_status'] == '3')
				<span style="color: blue; cursor: not-allowed;"><i class="layui-icon">&#xe650;</i><a href="javascript:void(0)" onclick="BindBankCard()" style="color: blue; cursor: pointer;">卡变更</a></span>
			@elseif($_info['bank_status'] == '4')
				<span><i class="lx-icon" id="name-icon2"></i>审核不通过, <a href="javascript:BindBankCard()" style="color: blue; cursor: pointer;">请重新上传</a></span>
			@endif
			<p id="text_va">{{ $_info['bank_remarks'] }}</p>
		</li>
		<li><em>绑定手机：</em>绑定的手机号码为: {{ substr_replace(substr($_info['phone'], (stripos($_info['phone'], '-') + 1)), '*****', 3, -3) }} <span onclick="EditPhoneAndEmail('phone')" style="color: blue; cursor: pointer;"><i class="layui-icon">&#xe620;</i>修改手机号</span></li></li>
		<li><em>邮箱设置：</em>您的邮箱: {{ substr_replace($_info['email'], '*****', 3, (stripos($_info['email'], '@') - 3)) }}  <span onclick="EditPhoneAndEmail('email')" style="color: blue; cursor: pointer;"><i class="layui-icon">&#xe620;</i>修改邮箱</span></li></li>
		<li><em>账户注销：</em>
			@if(count ($_info['cancel_info']) > 0)
				@if($_info['cancel_info'][0]['cancel_status'] == '0')
					<span style="color: blue; cursor: not-allowed;"><i class="layui-icon">&#xe650;</i>等待处理</span>
				@elseif($_info['cancel_info'][0]['cancel_status'] == '1')
					<span>处理成功</span>
				@elseif($_info['cancel_info'][0]['cancel_status'] == '-1')
					<span><i class="lx-icon" id="name-icon3"></i>处理失败, <a href="javascript:accountCancelApply()" style="color: blue; cursor: pointer;">请重新申请</a></span>
				@endif
			@else
				<span><a href="javascript:void(0);" onclick="accountCancelApply()" style="color: blue; cursor: pointer;">销户申请</a></span>
			@endif
			<p id="cancel_remark">
				@if(count ($_info['cancel_info']) > 0)
					{{ $_info['cancel_info'][0]['cancel_remark'] }}
				@endif
			</p>
		</li>
	</div>
	{{--<form class="layui-form" action="" id="UserInfoForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">账户ID</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">账户名称</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入账户名称" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">手机号码</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入账户名称" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">银行卡号</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入账户名称" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">邮箱</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入账户名称" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">上传图像</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入账户名称" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">销户申请</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入账户名称" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">是否认证</label>
				<div class="layui-input-inline">
					<select name="userstatus" id="userstatus">
						<option value="">请选择状态</option>
						<option value="0">未认证</option>
						<option value="1">已认证</option>
					</select>
				</div>
			</div>
			
			<div class="layui-inline">
				<label class="layui-form-label">开户时间</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入账户名称" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<button type="button" class="layui-btn" onclick="submitUserInfo()">确认修改</button>
		</div>
	</form>--}}
@endsection

@section('custom-resources')
	<script type="text/javascript">
	</script>
@endsection