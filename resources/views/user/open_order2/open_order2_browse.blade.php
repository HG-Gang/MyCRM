@extends('user.layout.main_right')

@section('content')
	<form class="layui-form" action="" id="OpenOrder2Form" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">订单号</label>
				<div class="layui-input-block">
					<input type="text" name="orderId" id="orderId" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">开仓时间</label>
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
	<table id="data_list" style="width: 99%;" pagination="true" title="未平仓单"></table>
@endsection

@section('custom-resources')
    <script type="text/javascript">
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'ticket' ,title:'{{ trans ('systemlanguage.open_order_ticket_id') }}', width:100,},
				{field:'login' ,title:'{{ trans ('systemlanguage.open_order_user_id') }}', width:100,},
				{field:'symbol' ,title:'{{ trans ('systemlanguage.open_order_symbol_type') }}', width:100},
				{field:'cmd' ,title:'{{ trans ('systemlanguage.open_order_cmd_type') }}', width:100, formatter: function (value, rowData, rowIndex) {
					if (rowData.ticket != '') {
						return getCmdTypeName(value);
					} else {
						return '';
					}
				}},
				{field:'volume' ,title:'{{ trans ('systemlanguage.open_order_volume') }}', width:100, align:'right', formatter: function (value, rowData, rowIndex) {
					if (rowData.ticket != '') {
						return parseFloat(value / 100).toFixed(2);
					} else {
						return value;
					}
				}},
				{field:'sl',title:'{{ trans ('systemlanguage.open_order_sl') }}', width:100, align:'right'},
				{field:'tp' ,title:'{{ trans ('systemlanguage.open_order_tp') }}', width:100, align:'right'},
				{field:'commission' ,title:'{{ trans ('systemlanguage.open_order_commission_money') }}', width:100, align:'right',
					formatter: function (value, rowData, rowIndex) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}},
				{field:'profit' ,title:'{{ trans ('systemlanguage.open_order_profit_money') }}' ,width:100 ,align:'right', formatter: function (value, rowData, rowIndex) {
					if (value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					} else {
						return parseFloatToFixed(value);
					}
				}},
				{field:'swaps' ,title:'{{ trans ('systemlanguage.open_order_swaps_money') }}' ,width:100 ,align:'right', formatter: function (value, rowData, rowIndex) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}},
				{field:'open_price' ,title:'{{ trans ('systemlanguage.open_order_open_price') }}', width:100, align:'right'},
				{field:'open_time' ,title:'{{ trans ('systemlanguage.open_order_time') }}' ,width:110},
			]];
			
			return config;
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息");
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: '/user/open/openOrder2Search',
				tableId: 'data_list',
				formId: 'OpenOrder2Form',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'ticket',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
			});
			
			pagerData.GridInit();
		}
	</script>
@endsection