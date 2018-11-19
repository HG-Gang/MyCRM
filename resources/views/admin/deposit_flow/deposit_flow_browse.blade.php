@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<form class="layui-form" action="" id="AdminDepositFlowForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">订单号</label>
				<div class="layui-input-block">
					<input type="text" name="deposit_id" id="deposit_id" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">入金类别</label>
				<div class="layui-input-inline">
					<select name="direct_deposit_source" id="direct_deposit_source">
						<option value="">请选择入金来源</option>
						<option value="-FY">账户返佣</option>
						<option value="-ZH">佣金转户</option>
						<option value="-TH">佣金退回</option>
						<option value="-CZ" selected>账户充值</option>
						<option value="-RJ">批量入金</option>
						<option value="-CJTH">出金退回</option>
						<option value="-Adj">平台入金</option>
					</select>
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">入金时间</label>
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
	<table id="deposit_data_list" style="width: 99%;" pagination="true" title="入金列表"></table>
@endsection

@section('custom-resources')
	<script>
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'order_no' ,title:'{{ trans ('systemlanguage.account_deposit_order_no') }}', width:100, align:'center',},
				{field:'userId' ,title:'{{ trans ('systemlanguage.account_deposit_no') }}', width:100, align:'center',},
				{field:'directProfit' ,title:'{{ trans ('systemlanguage.account_deposit_moneny') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					
					return parseFloatToFixed(value);
				}},
				{field:'depamount' ,title:'{{ trans ('systemlanguage.account_deposit_depamount') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (value) {
						return parseFloatToFixed(value);
					} else if (rowData.userId) {
						return "0.00";
					} else {
						return "";
					}
				}},
				{field:'directType' ,title:'{{ trans ('systemlanguage.account_deposit_type') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return getAccountdepositType(value);
				}},
				{field:'directComment' ,title:'{{ trans ('systemlanguage.account_deposit_comment') }}', width:100, align:'center',},
				{field:'depoutTrande' ,title:'{{ trans ('systemlanguage.account_deposit_flownumber') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
						if (value) {
							return value;
						} else if (rowData.userId) {
							return "==========";
						} else {
						    return '';
						}
				}},
				{field:'directModifyTime' ,title:'{{ trans ('systemlanguage.account_deposit_datetme') }}', width:100, align:'center',},
			]];
			
			config.Buttons = [{
				text: '{{ trans ('systemlanguage.export') }}',
				iconCls:'icon-export',
				handler:function(){
					flow_export("AdminDepositFlowForm", "depositFlow", "admin", "{{ csrf_token() }}")
				}
			}];
			
			return config;
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: route_prefix() + "/amount/depositFlowSearch",
				tableId: "deposit_data_list",
				formId: "AdminDepositFlowForm",
				method: 'post',
				columns : config.DataColumns,
				buttons: config.Buttons,
				formToken: "{{ csrf_token() }}",
				idField: 'order_no',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
			});
			
			pagerData.GridInit();
		}
		
		//双击更改直属客户组别信息
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息");
		}
	</script>
@endsection