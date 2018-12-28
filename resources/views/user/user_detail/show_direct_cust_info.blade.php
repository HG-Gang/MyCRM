@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<form class="layui-form" action="" style="margin-top: 20px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">账户ID</label>
				<div class="layui-input-block">
					<input type="text" name="userid" id="userid" value="{{ $_info['user_id'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">账户名称</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" value="{{ $_info['user_name'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">上级ID</label>
				<div class="layui-input-block">
					<input type="text" name="parent_id" id="parent_id" value="{{ $_info['parent_id'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">手机号码</label>
				<div class="layui-input-block">
					@if($role == 'admin' && $permit == 1)
						<input type="text" name="userphone" id="userphone" value="{{ substr($_info['phone'], (stripos($_info['phone'], '-') + 1)) }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@else
						<input type="text" name="userphone" id="userphone" value="{{ substr_replace(substr($_info['phone'], (stripos($_info['phone'], '-') + 1)), '*****', 3, -3) }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@endif
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">E-mail</label>
				<div class="layui-input-block">
					@if($role == 'admin' && ($permit == 1 || $permit == 2 || $permit == 3))
						<input type="text" name="useremail" id="useremail" value="{{ $_info['email'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@else
						<input type="text" name="useremail" id="useremail" value="{{ substr_replace($_info['email'], '*****', 3, (stripos($_info['email'], '@') - 3)) }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@endif
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">性别</label>
				<div class="layui-input-block">
					<input type="text" name="sex" id="sex" value="{{ $_info['sex'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
		</div>
		
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">账户余额</label>
				<div class="layui-input-block">
					<input type="text" name="usermoney" id="usermoney" value="{{ $_info['user_money'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">保证金</label>
				<div class="layui-input-block">
					<input type="text" name="availablebondmoney" id="availablebondmoney" value="{{ $_info['available_bond_money'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">信用额</label>
				<div class="layui-input-block">
					<input type="text" name="effectivecdt" id="effectivecdt" value="{{ $_info['effective_cdt'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
		</div>
		
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">账户模式</label>
				<div class="layui-input-block">
					@if($_info['trans_mode'] == '1')
						<input type="text" name="transmode" id="transmode" data-trans_mode="{{ $_info['trans_mode'] }}" value="权益模式" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@elseif($_info['trans_mode'] == '0')
						<input type="text" name="transmode" id="transmode" data-trans_mode="{{ $_info['trans_mode'] }}" value="佣金模式" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@endif
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">账户状态</label>
				<div class="layui-input-block">
					@if($_info['user_status'] == '1' && $_info['IDcard_status'] == '2' && $_info['bank_status'] == '2')
						<input type="text" name="userstatus" id="userstatus" data-user_status="{{ $_info['user_status'] }}" value="已认证" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@else
						<input type="text" name="userstatus" id="userstatus" data-user_status="{{ $_info['user_status'] }}" value="未认证" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@endif
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">出金状态</label>
				<div class="layui-input-block">
					@if($_info['is_out_money'] == '1')
						<input type="text" name="isoutmoney" id="isoutmoney" data-user_status="{{ $_info['is_out_money'] }}" value="不允许出金" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@else
						<input type="text" name="isoutmoney" id="isoutmoney" data-user_status="{{ $_info['is_out_money'] }}" value="允许出金" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
					@endif
				</div>
			</div>
		</div>
		
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">客户组别</label>
				<div class="layui-input-block">
					<input type="text" name="mt4_grp" id="mt4_grp" value="{{ $_info['mt4_grp'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">权益比例</label>
				<div class="layui-input-block">
					<input type="text" name="reccrtdate" id="reccrtdate" value="{{ $_info['rights'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">开户时间</label>
				<div class="layui-input-block">
					<input type="text" name="reccrtdate" id="reccrtdate" value="{{ $_info['rec_crt_date'] }}" autocomplete="off" class="layui-input" readonly="" style="width:200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">备注</label>
				<div class="layui-input-block">
					<input type="text" name="userremark" id="userremark" value="{{ $_info['remark'] }}" autocomplete="off" class="layui-input" style="width: 850px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
		</div>
	</form>
	@if($role == 'admin')
		<div style="margin:20px 0px;"></div>
		<table id="login_history" style="width: 99%;" pagination="true" title="登录历史记录"></table>
	@endif
@endsection

@section('custom-resources')
	@if($role == 'admin')
		<script type="text/javascript">
		$(function () {
			createTable();
		});
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'login_id' ,title:'{{ trans ('systemlanguage.history_loginId') }}', width:100, align:'center',},
				{field:'login_id_desc' ,title:'{{ trans ('systemlanguage.history_loginIddesc') }}', width:100, align:'center',},
				{field:'login_ip' ,title:'{{ trans ('systemlanguage.history_loginIdIp') }}', width:100, align:'center',},
				{field:'login_date' ,title:'{{ trans ('systemlanguage.history_date') }}', width:100, align:'center',},
			]];
			
			return config;
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息");
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: '/user/cust/loginHistorySearch/' + '{{ $_info['user_id'] }}',
				tableId: 'login_history',
				formId: '',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'login_id',
				extraParam: '',
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
				footerMsg: "",
			});
			
			pagerData.GridInit();
		}
	</script>
	@endif
@endsection