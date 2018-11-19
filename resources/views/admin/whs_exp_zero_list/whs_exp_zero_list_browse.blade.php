@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
	<form class="layui-form" action="" id="AdminWhsExpZeroForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">爆仓账号</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入爆仓账号" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">爆仓姓名</label>
				<div class="layui-input-block">
					<input type="text" name="wez_username" id="wez_username" autocomplete="off" placeholder="请输入爆仓姓名" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">返佣时间</label>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="startdate" id="startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
				</div>
				<div class="layui-form-mid">-</div>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="enddate" id="enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
				</div>
			</div>
			<button type="button" class="layui-btn" onclick="createTable()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="爆仓列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'wezuserid' ,title:'{{ trans ('systemlanguage.whs_exp_acc_no') }}', width:100, align:'center',},
				{field:'wezusername' ,title:'{{ trans ('systemlanguage.whs_exp_acc_name') }}', width:100, align:'center',},
				{field:'wezuserbal' ,title:'{{ trans ('systemlanguage.whs_exp_bal') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				    return parseFloatToFixed(value);
				}},
				{field:'wezusercrt' ,title:'{{ trans ('systemlanguage.whs_exp_crt') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				    return parseFloatToFixed(value);
				}},
				{field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.whs_exp_is_zero_date') }}', width:100, align:'center',},
			]];
			
			return config;
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息!");
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: route_prefix() + '/order/whsExpZeroListSearch',
				tableId: 'data_list',
				formId: 'AdminWhsExpZeroForm',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'wezuserid',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
				});
			
			pagerData.GridInit();
		}
	</script>
@endsection