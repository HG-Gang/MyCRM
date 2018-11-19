@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
	<form class="layui-form" action="" id="AdminRealtimeRebateForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">订单号</label>
				<div class="layui-input-block">
					<input type="text" name="orderId" id="orderId" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">返佣时间</label>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="startdate" id="startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input" value="{{ date("Y-m-d") }}">
				</div>
				<div class="layui-form-mid">-</div>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="enddate" id="enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input" value="{{ date("Y-m-d") }}">
				</div>
			</div>
			<button type="button" class="layui-btn" onclick="createTable()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="实时返佣"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'ticket' ,title:'{{ trans ('systemlanguage.real_rebate_ticket_id') }}', width:100, align:'center',},
				{field:'login' ,title:'{{ trans ('systemlanguage.real_rebate_user_id') }}', width:100, align:'center',},
				{field:'profit' ,title:'{{ trans ('systemlanguage.real_rebate_order_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				    return parseFloatToFixed(value);
				}},
				{field:'comment' ,title:'{{ trans ('systemlanguage.real_rebate_order_source') }}', width:100, align:'center',},
				{field:'modify_time' ,title:'{{ trans ('systemlanguage.real_rebate_order_time') }}', width:100, align:'center',},
			]];
			
			return config;
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			var orderNo = trades_detail(rowData.comment);
			show_rebate_order_detail(orderNo, 'admin');
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: route_prefix() + '/order/realCommissionListSearch',
				tableId: 'data_list',
				formId: 'AdminRealtimeRebateForm',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'ticket',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
				footerMsg: "双击行显示详情",
			});
			
			pagerData.GridInit();
		}
	</script>
@endsection