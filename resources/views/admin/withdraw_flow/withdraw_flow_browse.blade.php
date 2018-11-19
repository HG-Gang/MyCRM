@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<form class="layui-form" action="" id="AdminWithdrawFlowForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">订单号</label>
				<div class="layui-input-block">
					<input type="text" name="withdraw_id" id="withdraw_id" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">出金类别</label>
				<div class="layui-input-inline">
					<select name="withdraw_source" id="withdraw_source">
						<option value="">请选择入金来源</option>
						<option value="-ZH">佣金转户</option>
						<option value="-TH">佣金退回</option>
						<option value="-QK">账户取款</option>
						<option value="-CJTH">出金退回</option>
						<option value="-Adj">平台出金</option>
					</select>
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">出金时间</label>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="deposit_startdate" id="deposit_startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
				</div>
				<div class="layui-form-mid">-</div>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="deposit_enddate" id="deposit_enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
				</div>
			</div>
			<button type="button" class="layui-btn" onclick="createTable()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="withdraw_data_list" style="width: 99%;" pagination="true" title="出金列表"></table>
@endsection

@section('custom-resources')
	<script>
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'order_no' ,title:'{{ trans ('systemlanguage.account_withdraw_order_no') }}', width:100, align:'center',},
				{field:'userId' ,title:'{{ trans ('systemlanguage.account_withdraw_no') }}', width:100, align:'center',},
				{field:'directProfit' ,title:'{{ trans ('systemlanguage.account_withdraw_moneny') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}},
				{field:'directType' ,title:'{{ trans ('systemlanguage.account_withdraw_type') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return getAccountdepositType(value);
				}},
				{field:'directComment' ,title:'{{ trans ('systemlanguage.account_withdraw_comment') }}', width:100, align:'center',},
				{field:'directModifyTime' ,title:'{{ trans ('systemlanguage.account_withdraw_datetme') }}', width:100, align:'center',},
			]];
			
			/*config.Buttons = [{
				text: '{{ trans ('systemlanguage.export') }}',
				iconCls:'icon-export',
				handler:function(){
					flow_export("AdminWithdrawFlowForm", "withdrawFlow", "admin", "{{ csrf_token() }}")
				}
			}];*/
			
			return config;
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: route_prefix() + "/amount/withdrawFlowSearch",
				tableId: "withdraw_data_list",
				formId: "AdminWithdrawFlowForm",
				method: 'post',
				columns : config.DataColumns,
				buttons: config.Buttons,
				formToken: "{{ csrf_token() }}",
				idField: 'order_no',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: false,
			});
			
			pagerData.GridInit();
		}
		
		//双击更改直属客户组别信息
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息");
		}
	</script>
@endsection