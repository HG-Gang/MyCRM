@extends('user.layout.main_right')

@section('content')
	<form class="layui-form" action="" id="CloseOrderForm" style="margin-top: 8px;">
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
				<label class="layui-form-label">是否强平</label>
				<div class="layui-input-inline">
					<select name="is_coercion" id="is_coercion">
						<option value="">请选择订单类型</option>
						<option value="Yes">是</option>
						<option value="No">否</option>
					</select>
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">平仓时间</label>
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
	<table id="data_list" style="width: 99%;" pagination="true" title="已平仓单"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'ticket' ,title:'{{ trans ('systemlanguage.close_order_ticket_id') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (value) {
						return "<font  onclick='orderIdDetail("+ value +", "+'"closeOrder"'+", "+'"agents"'+")' style='color: blue; cursor: pointer;' title='查看订单详情'>" + value + "</font>";
					}
				}},
				{field:'login' ,title:'{{ trans ('systemlanguage.close_order_user_id') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (value == '{{ $_user_info['user_id'] }}') {
						return "<span style='color: #1AA094;'>" + value + "</span>";
					} else {
						return "<span onclick='userIdDetail("+ value +", "+'"agents"'+")' style='color: blue; cursor: pointer;' title='查看账号详情'>" + value + "</span>";
					}
				}},
				{field:'symbol' ,title:'{{ trans ('systemlanguage.close_order_symbol_type') }}', width:100, align:'center', },
				{field:'cmd' ,title:'{{ trans ('systemlanguage.close_order_cmd_type') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.ticket != '') {
						return getCmdTypeName(value);
					} else {
						return '';
					}
				}},
				{field:'volume' ,title:'{{ trans ('systemlanguage.close_order_volume') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.ticket != '') {
						return parseFloat(value / 100).toFixed(2);
					} else {
						return value;
					}
				}},
				{field:'sl',title:'{{ trans ('systemlanguage.close_order_sl') }}', width:100, align:'center'},
				{field:'tp' ,title:'{{ trans ('systemlanguage.close_order_tp') }}', width:100, align:'center'},
				{field:'commission' ,title:'{{ trans ('systemlanguage.close_order_commission_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}},
				{field:'profit' ,title:'{{ trans ('systemlanguage.close_order_profit_money') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
					if (value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					} else {
						return parseFloatToFixed(value);
					}
				}},
				{field:'swaps' ,title:'{{ trans ('systemlanguage.close_order_swaps_money') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
					if (value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}

					return value;
				}},
				{field:'open_price' ,title:'{{ trans ('systemlanguage.close_order_open_price') }}', width:100, align:'center',},
				{field:'close_price' ,title:'{{ trans ('systemlanguage.close_order_price') }}' ,width:100, align:'center',},
				{field:'close_time' ,title:'{{ trans ('systemlanguage.close_order_time') }}' ,width:110, align:'center',},
			]];
			
			return config;
		}
		
		function subAccountDetail(uid) {
			getExtraParam(uid);
			createTable();
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息!");
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: '/user/close/closeOrderSearch',
				tableId: 'data_list',
				formId: 'CloseOrderForm',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'ticket',
				rowStyler: true,
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
			});
			
			pagerData.GridInit();
		}
		
		function getExtraParam(uid) {
			subPuid.ispage = true;
			subPuid.userPId = uid;
		}
	</script>
@endsection