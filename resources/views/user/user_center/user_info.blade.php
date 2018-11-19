@extends('user.layout.main_right')
<style>
	body{overflow-y:hidden;}
	.a-upload{padding: 4px 10px;height: 27px;line-height: 28px;position: relative;top: -13px;left: -14px;cursor: pointer;/*color: #888; background: #fafafa;border: 1px solid #ddd;*/border-radius: 27px;overflow: hidden;display: inline-block;*display: inline;*zoom: 1;}
	.a-upload input{position: absolute;font-size: 100px;right: 0;top: 0;opacity: 0;filter: alpha(opacity=0);cursor: pointer;}
	.a-upload:hover{/*color: #444;background: #eee;border-color: #ccc;*/text-decoration: none;}
	.header_div{height: 600px;}
	.user_name_top {height: 115px;margin: 20px 0px;}
	.l {float: left;}
	.r {float: right;}
	.left {width:145px;height: 130px;margin-left: 5%;position: relative;}
	.left > img{border-radius:50%;}
	.left > p {width: 80px; height: 30px; line-height: 30px;display: none;  position: absolute; bottom: 0; text-align: center; left: 38px; color: #fff; cursor: pointer;}
	.left:hover > p {display: block;}
	.rigth {width: 70%;}
	.rigth>ul {margin: 0 ;padding: 0;height: 130px;}
	.rigth > ul > li {height:30px;}
	rigth > ul > li>P {height: 50px;line-height: 50px;}
	.list_sum > ul > li {height: 80px;line-height: 80px;}
	.list_sum > ul>li>ul>li {display: inline-block;float: left;width:33.3%;height: 80px;line-height: 80px;border-bottom: 1px dashed #ccc;}
	.list_sum > ul > li > ul > li:nth-child(1) {text-indent: 2em;}
</style>

@section('public-resources')

@endsection

@section('content')
	<div class="header_div" >
		<div class="user_name_top">
			<div class="left l">
				@if(!empty($_info['img']) && $_info['img'][0]['img_header_path'] != null)
					<img src="{{ URL::asset($_info['img'][0]['img_header_path']) }}?ver={{ resource_version_number() }}" alt="上传头像" style="width: 130px; height: 130px;">
				@else
					<img src="{{ URL::asset('img/user_header.jpg') }}?ver={{ resource_version_number() }}" alt="上传头像" style="width: 130px; height: 130px;">
				@endif
				{{--<img src="{{ URL::asset('img/user_header.jpg') }}" alt="上传头像" style="width: 130px; height: 130px;">--}}
				<p><a href="javascript:void(0);" class="a-upload" onclick="uploadHeader()">上传头像</a></p>
			</div>
			<div class="rigth l">
				<ul >
					<li style="margin-top: 40px;">
						<p>账户ID: {{ $_info['user_id'] }}</p>
						
					</li>
					<li>
						<p>账户名称: {{ $_info['user_name'] }}</p>
					</li>
				</ul>
			</div>
		</div>
		<div class="list_sum">
			<ul>
				<li>
					<ul>
						<li>实名认证</li>
						<li>身份证号码: {{ substr_replace($_info['IDcard_no'], '********', 6, -4) }}</li>
						<li>
							@if($_info['IDcard_status'] == '0')
								<span>待上传, <a href="javascript:void(0)" onclick="uploadIdCard()" style="color: blue; cursor: pointer;">请上传资料</a></span>
							@elseif($_info['IDcard_status'] == '1')
								<span style="color: blue; cursor: not-allowed;"><i class="layui-icon">&#xe650;</i>正在审核中</span>
							@elseif($_info['IDcard_status'] == "2")
								<span>已实名认证</span>
							@elseif($_info['IDcard_status'] == "4")
								<span><i class="lx-icon" id="name-icon"></i><a href="javascript:void(0)" onclick="uploadIdCard()" style="color: blue; cursor: pointer;">请重新上传资料</a></span>
							@endif
						</li>
					</ul>
				</li>
				<li>
					<ul>
						<li>取款银行卡</li>
						<li>银行卡号: {{ substr_replace($_info['bank_no'], '**********', 4, -4) }}</li>
						<li>
							@if($_info['bank_status'] == '0')
								<span onclick="uploadBank()" style="color: blue; cursor: pointer;"><i class="layui-icon">&#xe620;</i>待上传</span>
							@elseif($_info['bank_status'] == '1')
								<span style="color: blue; cursor: not-allowed;"><i class="layui-icon">&#xe650;</i>正在审核中</span>
							@elseif($_info['bank_status'] == '2')
								<span>审核通过</span>
								@if(!empty($_info['is_change']) && $_info['is_change'] == 'allowChange')
									<span>&nbsp;&nbsp;&nbsp;</span>
									<span style="color: blue; cursor: pointer;" onclick="uploadBankChange('changeBank')"><i class="layui-icon">&#xe620;</i>银行卡变更</span>
								@endif
							@elseif($_info['bank_status'] == '3')
								<span style="color: blue; cursor: not-allowed;"><i class="layui-icon">&#xe650;</i><a href="javascript:void(0)"  style="color: blue; cursor: pointer;">银行卡变更</a></span>
							@elseif($_info['bank_status'] == '4')
								<span><i class="lx-icon" id="name-icon2"></i><a href="javascript:void(0)" onclick="uploadBank()" style="color: blue; cursor: pointer;">请重新上传</a></span>
							@endif
							@if($_info['bank_status'] == '4') <span>&nbsp;&nbsp;&nbsp;原因: {{ $_info['bank_remarks'] }}</span> @endif
						</li>
						
					</ul>
				</li>
				<li>
					<ul>
						<li>绑定手机</li>
						<li>手机号码: {{ substr_replace(substr($_info['phone'], (stripos($_info['phone'], '-') + 1)), '*****', 3, -3) }}</li>
						<li>
							<span onclick="updatePhoneEmail('phone')" style="color: blue; cursor: pointer;"><i class="layui-icon">&#xe620;</i>修改手机号</span>
						</li>
					</ul>
				</li>
				<li>
					<ul>
						<li>邮箱设置</li>
						<li>邮箱: {{ substr_replace($_info['email'], '*****', 3, (stripos($_info['email'], '@') - 3)) }}</li>
						<li>
							<span onclick="updatePhoneEmail('email')" style="color: blue; cursor: pointer;"><i class="layui-icon">&#xe620;</i>修改邮箱</span>
						</li>
					</ul>
				</li>
				<li>
					<ul>
						<li>账户注销</li>
						<li></li>
						<li>
							@if(count ($_info['cancel_info']) > 0)
								@if($_info['cancel_info'][0]['cancel_status'] == '0')
									<span style="color: blue; cursor: not-allowed;"><i class="layui-icon">&#xe650;</i>等待处理</span>
								@elseif($_info['cancel_info'][0]['cancel_status'] == '1')
									<span>处理成功</span>
								@elseif($_info['cancel_info'][0]['cancel_status'] == '-1')
									<span><i class="lx-icon" id="name-icon3"></i>处理失败, <a href="javascript:void(0)" onclick="accountCancelApply()" style="color: blue; cursor: pointer;">请重新申请</a></span>
								@endif
							@else
								<span><a href="javascript:void(0);" onclick="accountCancelApply()" style="color: blue; cursor: pointer;">销户申请</a></span>
							@endif
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function uploadHeader() {
			var index = layer.open({
				type: 2,
				title: "上传头像",
				skin: 'layui-layer-molv',
				move:false,
				area: ['600px', '485px'],
				content: '/user/center/uploadHead_browse/',
			});
		}
	</script>
@endsection