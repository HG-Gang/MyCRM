@extends('user.layout.main_right')

@section('public-resources')
	<style>
		html{overflow-x: hidden; overflow-y: hidden; }
		*{margin: 0 ; padding: 0; font-size: 15px; }
		.header{width: 80%; height: 35px; line-height: 35px; text-indent: 2em; font-weight: 600; }
		.openlist-sum{width: 100%; height: 40px; position: relative; background: #f2f2f2; }
		.openlist-sum-l{width: 33%; float: left; height: 40px; position: relative; }
		.openlist-sum-l>.child-tl{display: inline-block; height: 35px; line-height: 40px; width: 30%; text-align: right; }
		.openlist-sum-l>.child-tr{width: 56%; display: inline-block; height: 35px; line-height: 35px; padding-left: 5px; padding-right: 5px; text-indent: 0.5em; border-bottom: 1px solid #ccc; }
		.openlist-sum-r{width: 33%; float: left; height: 40px; position: relative; }
		.openlist-sum-r>.child-tl{display: inline-block; height: 35px; line-height: 35px; width: 32%; text-align: right; float: left; }
		.openlist-sum-r>.child-tr{display: inline-block; height: 38px; line-height: 37px; padding-left: 5px; padding-right: 5px; text-indent: 0.5em; border-bottom: 1px solid #ccc; width: 60%; }
		.openlist-sum-t{width: 33%; float: left; height: 40px; position: relative; }
		.openlist-sum-t>.child-tl{display: inline-block; height: 35px; line-height: 35px; width: 30%; text-align: right; }
		.openlist-sum-t>.child-tr{display: inline-block; height: 38px; line-height: 37px; padding-left: 5px; padding-right: 5px; text-indent: 0.5em; border-bottom: 1px solid #ccc; width: 60%; }
		.box{width: 100%; height: 499px; border: 1px solid #ccc; margin: 0 auto; }
		.layui-layer{height: 572px !important; }
	</style>
@endsection

@section('content')
		<p style="display:block;margin-left: 32px;font-size: 16px;font-weight: 600; color: brown;">{{ $_user_info['user_rala'] }}</p>
	
	<div class="box">
		<div class="header">
			<p>基本信息</p>
		</div>
		
		<div class="openlist-sum">
			<div class="openlist-sum-l">
				<p class="child-tl">账户编号:</p>
				<p class="child-tr" style="font-weight: 600;">{{ $_user_info['user_id'] }}</p>
			</div>
			<div class="openlist-sum-r">
				<p class="child-tl">账户名字:</p>
				<p class="child-tr" style="font-weight: 600;">{{ $_user_info['user_name'] }}</p>
			</div>
			
			
			<div class="openlist-sum-t">
				<p class="child-tl">性别:</p>
				<p class="child-tr">{{ $_user_info['sex'] }}</p>
			</div>
		</div>
		
		<div class="openlist-sum">
			@if($_user_info['role'] == 'admin')
				<div class="openlist-sum-l">
					<p class="child-tl">账户手机号:</p>
					<p class="child-tr">{{ substr($_user_info['phone'], (stripos($_user_info['phone'], '-') + 1)) }}</p>
				</div>
				<div class="openlist-sum-r">
					<p class="child-tl">账户邮箱:</p>
					<p class="child-tr">{{ $_user_info['email'] }}</p>
				</div>
			@elseif($_user_info['direct_user'] == 'TRUE' && $_user_info['role'] == 'agents')
				<div class="openlist-sum-l">
					<p class="child-tl">账户手机号:</p>
					<p class="child-tr">{{ substr_replace(substr($_user_info['phone'], (stripos($_user_info['phone'], '-') + 1)), '*****', 3, -3) }}</p>
				</div>
				<div class="openlist-sum-r">
					<p class="child-tl">账户邮箱:</p>
					<p class="child-tr">{{ substr_replace($_user_info['email'], '*****', 3, (stripos($_user_info['email'], '@') - 3)) }}</p>
				</div>
			@endif
			<div class="openlist-sum-t">
				<p class="child-tl">上级编号:</p>
				<p class="child-tr">{{ $_user_info['parent_id'] }}</p>
			</div>
		</div>
		@if($_user_info['role'] == 'admin')
			<div class="header">
				<p>账户状态</p>
			</div>
			<div class="openlist-sum">
				<div class="openlist-sum-l">
					<p class="child-tl">身份证资料:</p>
					@if($_user_info['IDcard_status'] == '0')
						<p class="child-tr">待上传</p>
					@elseif($_user_info['IDcard_status'] == '1')
						<p class="child-tr">正在审核中</p>
					@elseif($_user_info['IDcard_status'] === '2')
						<p class="child-tr">审核通过</p>
					@elseif($_user_info['IDcard_status'] === '4')
						<p class="child-tr">退回</p>
					@endif
				</div>
				<div class="openlist-sum-r">
					<p class="child-tl">银行卡资料:</p>
					@if($_user_info['bank_status'] == '0')
						<p class="child-tr"> 待上传</p>
					@elseif($_user_info['bank_status'] == '1')
						<p class="child-tr">正在审核中</p>
					@elseif($_user_info['bank_status'] === '2')
						<p class="child-tr">审核通过</p>
					@elseif($_user_info['bank_status'] === '3')
						<p class="child-tr"> 银行卡变更</p>
					@elseif($_user_info['bank_status'] === '4')
						<p class="child-tr">退回</p>
					@endif
				</div>
				<div class="openlist-sum-t">
					<p class="child-tl">是否认证:</p>
					@if($_user_info['user_status'] == '1' && $_user_info['IDcard_status'] == '2' && $_user_info['bank_status'] == '2')
						<p class="child-tr" style="color: green;">已认证</p>
					@else
						<p class="child-tr" style="color: red;">未认证</p>
					@endif
				</div>
			</div>
			<div class="openlist-sum">
				<div class="openlist-sum-l">
					<p class="child-tl">身份证号:</p>
					@if ($_user_info['role'] == 'admin')
						<p class="child-tr">{{ $_user_info['IDcard_no'] }}</p>
					@else
						<p class="child-tr">{{ substr_replace($_info['IDcard_no'], '********', 6, -4) }}</p>
					@endif
				</div>
				<div class="openlist-sum-r">
					<p class="child-tl" style="">银行卡号:</p>
					@if($_user_info['role'] == 'admin')
						@if($_user_info['bank_status'] == '2')
							<p class="child-tr"> {{ $_user_info['bank_no'] }}</p>
						@else
							<p class="child-tr"> {{ $_user_info['bank_no_tmp'] }}</p>
						@endif
					@else
						@if($_user_info['bank_status'] == '2')
							<p class="child-tr"> {{ substr_replace($_info['bank_no'], '**********', 4, -4) }}</p>
						@else
							<p class="child-tr"> {{ substr_replace($_info['bank_no_tmp'], '**********', 4, -4) }}</p>
						@endif
					@endif
				</div>
				{{--<div class="openlist-sum-t">
					<p class="child-tl">账户类型:</p>
					@if((int)$_user_info['user_id'] >= 8000001)
						@if($_user_info['grp_id'] == '1')
							<p class="child-tr">有佣金账户</p>
						@else
							<p class="child-tr">无佣金账户</p>
						@endif
					@else
						<p class="child-tr">{{ $_user_info['agrp_name'] }}</p>
					@endif
				</div>--}}
			</div>
			<div class="openlist-sum">
				<div class="openlist-sum-l" style="position: absolute;left: 0px;">
					<p class="child-tl">是否启用:</p>
					@if($_user_info['enable'] == 1)
						<p class="child-tr" style="color: green;">启用</p>
					@else
						<p class="child-tr" style="color: red;">禁用</p>
					@endif
				</div>
				<div class="openlist-sum-r" style="position: absolute;left: 33.3%;">
					<p class="child-tl">是否只读:</p>
					@if($_user_info['enable_readonly'] == 1)
						<p class="child-tr" style="color: green;">是</p>
					@else
						<p class="child-tr" style="color: red;">否</p>
					@endif
				</div>
				<div class="openlist-sum-t" style="position: absolute;right: 10px;">
					<p class="child-tl">能否出金:</p>
					@if($_user_info['is_out_money'] == 1)
						<p class="child-tr" style="color: red; width: 193px;">禁止出金</p>
					@else
						<p class="child-tr" style="color: green; width: 193px;">正常出金</p>
					@endif
				</div>
			</div>
		@endif
		
		<div class="header">
			<p>账户资金</p>
		</div>
		<div class="openlist-sum">
			<div class="openlist-sum-l">
				<p class="child-tl">账户余额:</p>
				@if($_user_info['user_money'] > 0 )
					<p class="child-tr" style="color: #6cabb3;">{{ $_user_info['user_money'] }}</p>
				@elseif($_user_info['user_money'] <= 0)
					<p class="child-tr" style="color: red;">{{ $_user_info['user_money'] }}</p>
				@endif
			</div>
			<div class="openlist-sum-r">
				<p class="child-tl">账户净值:</p>
				@if($_user_info['cust_eqy'] > 0 )
					<p class="child-tr" style="color: #ae5376;">{{ $_user_info['cust_eqy'] }}</p>
				@elseif($_user_info['cust_eqy'] <= 0)
					<p class="child-tr" style="color: red;">{{$_user_info['cust_eqy'] }}</p>
				@endif
			</div>
			<div class="openlist-sum-t">
				<p class="child-tl">信用额:</p>
				@if($_user_info['effective_cdt'] > 0 )
					<p class="child-tr" style="color: #ae5376;">{{ $_user_info['effective_cdt'] }}<p>
				@elseif($_user_info['effective_cdt'] <= 0)
					<p class="child-tr" style="color: red;">{{$_user_info['effective_cdt'] }}<p>
				@endif
			</div>
		</div>
		
		@if(($_user_info == 'ADMINCLOSEORDER' || $_user_info == 'ADMINOPENORDER') && $_user_info['trans_mode'] == '1')
			<div class="openlist-sum">
				<div class="openlist-sum-l">
					<p class="child-tl">保证金额:</p>
					@if($_user_info['available_bond_money'] > 0 )
						<p class="child-tr" style="color: #6cabb3;">{{ $_user_info['available_bond_money'] }}</p>
					@elseif($_user_info['available_bond_money'] <= 0 )
						<p class="child-tr" style="color:red;">{{ $_user_info['available_bond_money'] }}</p>
					@endif
				</div>
				<div class="openlist-sum-r">
					<p class="child-tl">可用保证金:</p>
					@if($_user_info['used_bond_money'] > 0 )
						<p class="child-tr" style="color: #6cabb3;">{{ $_user_info['used_bond_money'] }}</p>
					@elseif($_user_info['used_bond_money'] <= 0 )
						<p class="child-tr" style="color:red;">{{ $_user_info['used_bond_money'] }}</p>
					@endif
				</div>
			</div>
		@endif
		
		
		<div class="openlist-sum">
			<div class="openlist-sum-l">
				<p class="child-tl">交易单量:</p>
				<p class="child-tr">{{ $_user_info['close'] }}<span style="font-size: 12px; color: green;">&nbsp;(已平)</span> / {{ $_user_info['open'] }}<span style="font-size: 12px; color: red;">&nbsp;(未平)</span></p>
			</div>
			@if((int)$_user_info['user_id'] < 8000001)
				<div class="openlist-sum-r">
					<p class="child-tl">返佣比例:</p>
					<p class="child-tr">{{ $_user_info['comm_prop'] }}</p>
				</div>
			@endif
			<div class="openlist-sum-t">
				<p class="child-tl">账户杠杆:</p>
				<p class="child-tr">{{ $_user_info['cust_lvg'] }}</p>
			</div>
		</div>
		@if(($_user_info['role'] == 'admin'))
			<div class="header">
				<p>账户类别</p>
			</div>
			<div class="openlist-sum">
				<div class="openlist-sum-l">
					<p class="child-tl">账户组别:</p>
					<p class="child-tr">{{ $_user_info['mt4_grp'] }}</p>
				</div>
				<div class="openlist-sum-r">
					<p class="child-tl">交易模式:</p>
					@if($_user_info['trans_mode'] == 1)
						<p class="child-tr" style="color: #0044BB;">权益模式</p>
					@else
						<p class="child-tr" style="color: #00AA55;">返佣模式</p>
					@endif
				</div>
			</div>
		@endif
		
		<div class="openlist-sum">
			<div class="openlist-sum-l">
				<p class="child-tl">开户时间:</p>
				<p class="child-tr">{{ $_user_info['rec_crt_date'] }}</p>
			</div>
			<div class="openlist-sum-r" style="display: none;">
				<p class="child-tl">最后登录时间:</p>
				<p class="child-tr">{{ $_user_info['last_logindate'] }}</p>
			</div>
		</div>
	</div>
@endsection

@section('custom-resources')
@endsection