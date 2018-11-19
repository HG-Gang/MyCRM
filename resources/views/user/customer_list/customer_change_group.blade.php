@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<div>
		<ul style="background: #fef7e4; text-indent: 2em;font-size: 14px; text-align: center;">
			{{--<ol>• 每月{{ $datetime[0] }}日{{ $datetime['1'] }} 时前提交转换申请</ol>
            <ol>• 账号将于每月最后一个交易日的结算时间进行转换</ol>--}}
			<ol>• 进行转换时账户内如有未平仓交易单或者有挂单订单, 则不能进行转换</ol>
		</ul>
	</div>
	<form class="layui-form" action="" id="changeGroupForm" style="margin-top: 8px; text-align: center;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">账户ID</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId"  value="{{ $_rs[0]['user_id'] }}" autocomplete="off" placeholder="请输入交易账户" class="layui-input" disabled="" style="width: 200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">账户名称</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" value="{{ $_rs[0]['user_name'] }}" autocomplete="off" placeholder="请输入账户名称" class="layui-input" disabled="" style="width: 200px; background: #e9e9e9; color: #0066ff;">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">账户类型</label>
				@if($_rs[0]['usergroupid'] == '1')
					<input type="text" name="groupname" id="groupname" autocomplete="off" value="有佣金" data-group_id="{{ $_rs[0]['usergroupid'] }}" data-group_name="{{ $_rs[0]['mt4_grp'] }}"placeholder="请输入账户类型" class="layui-input" disabled="" style="width: 200px; background: #e9e9e9; color: #0066ff;">
				@elseif($_rs[0]['usergroupid'] == '0')
					<input type="text" name="groupname" id="groupname" autocomplete="off" value="无佣金" data-group_id="{{ $_rs[0]['usergroupid'] }}" data-group_name="{{ $_rs[0]['mt4_grp'] }}" placeholder="请输入账户类型" class="layui-input" disabled="" style="width: 200px; background: #e9e9e9; color: #0066ff;">
				@endif
			</div>
		</div>
		<div class="layui-form-item" pane="">
			<div class="layui-inline">
				<label class="layui-form-label">变更类型</label>
				<div class="layui-input-block">
					<input type="radio" name="user_group_name" value="{{ $_rs['grp_type'][0]['user_group_name'] }}" title="@if($_rs['grp_type'][0]['group_id'] == '1')有佣金@elseif($_rs['grp_type'][0]['group_id'] == '0')无佣金@endif" {{ ($_rs['grp_type'][0]['user_group_name'] == $_rs[0]['mt4_grp']) ? 'checked' : '' }}>
					<input type="radio" name="user_group_name" value="{{ $_rs['grp_type'][1]['user_group_name'] }}" title="@if($_rs['grp_type'][1]['group_id'] == '1')有佣金@elseif($_rs['grp_type'][1]['group_id'] == '0')无佣金@endif" {{ ($_rs['grp_type'][1]['user_group_name'] == $_rs[0]['mt4_grp']) ? 'checked' : '' }}>
				</div>
			</div>
		</div>
		
		
		
		<div class="layui-form-item" style="text-align: center;">
			@if($_rs['isExistsOrder'] == 0)
				@if($_rs['isExistsTrans'] == 'YES')
					{{--有申请记录--}}
					<div>
						<span>温馨提醒 </span>
						<span>当前账号类别是: </span>
						@if($_rs[0]['usergroupid'] == '1' && ($_rs['grp_type'][0]['user_group_name'] == $_rs[0]['mt4_grp']))
							<span style="color:#0066ff; font-size: 15px;">有佣金</span>
						@elseif($_rs[0]['usergroupid'] == '0' && ($_rs['grp_type'][0]['user_group_name'] == $_rs[0]['mt4_grp']))
							<span style="color:#0066ff; font-size: 15px;">无佣金</span>
						@endif
						<span>正在申请: </span>
						@if($_rs['chk_trans']['trans_type_gid'] == '1')
							<span style="color:#CD853F; font-size: 15px;">有佣金</span>
						@elseif($_rs['chk_trans']['trans_type_gid'] == '0')
							<span style="color:#CD853F; font-size: 15px;">无佣金</span>
						@endif
					</div>
				@elseif($_rs['isExistsTrans'] == 'NO')
					<div class="layui-input-block">
						<button type="button" class="layui-btn" onclick="changeDirectCustGroupType()">提交申请</button>
					</div>
				@endif
			@else
				<div>
					<span>温馨提醒 </span>
					<span style="color: red;">当前账号有持仓单，无法申请变更</span>
				</div>
			@endif
		</div>
	</form>
@endsection

@section('custom-resources')
	<script>
		//更改直属客户组别类型
		function changeDirectCustGroupType() {
			var index = openLoadShade();
			var old_grp_name = "{{ $_rs[0]['mt4_grp'] }}";
			var grp_name = $("input[name='user_group_name']:checked").val();
			if (old_grp_name == grp_name) {
				layer.msg("申请的变更账户类型不能与原账户类型一样!");
				return;
			}
			
			$.ajax({
				url: "/user/cust/change/group_edit",
				data: {
					userId: "{{$_rs[0]['user_id']}}",
					grpName: grp_name,
					_token: "{{ csrf_token() }}",
				},
				dateType: "JSON",
				type: "POST",
				async: false,
				success: function(data) {
					if(data.msg == "SUCCESS") {
						closeLoadShade(index);
						layer.msg("申请成功!", {
							icon: 1,
							time: 20000, //20s后自动关闭
							btn: ['知道了'],
							yes: function (index, layero) {
								parent.layer.closeAll();
							}
						});
					} else if(data.msg == "FAIL") {
						layer.msg('申请失败,请重新操作!');
					} else if(data.msg == "CLASSINVALID") {
						layer.msg('账户类别无效!');
						return;
					}
				},
				error: function () {
					layer.msg("未知错误,请尝试重新操作或联系客服.");
				}
			});
		}
	</script>
@endsection