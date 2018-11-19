@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<form class="layui-form" action="" id="AdminWithdrawApplyForm" style="margin-top: 8px;">
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
				<label class="layui-form-label">出金状态</label>
				<div class="layui-input-inline">
					<select name="withdraw_source" id="withdraw_source">
						<option value="">请选择出金状态</option>
						<option value="0" selected>待处理</option>
						<option value="2">已处理</option>
						<option value="3">处理失败</option>
					</select>
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">申请时间</label>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="withdraw_startdate" id="withdraw_startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
				</div>
				<div class="layui-form-mid">-</div>
				<div class="layui-input-inline" style="width: 200px;">
					<input type="text" name="withdraw_enddate" id="withdraw_enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
				</div>
			</div>
			<button type="button" class="layui-btn" onclick="createTable()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<table id="withdraw_data_list" style="width: 99%;" pagination="true" title="出金申请列表"></table>
@endsection

@section('custom-resources')
	<script>
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'mt4_ticket' ,title:'{{ trans ('systemlanguage.account_apply_order_no') }}', width:100, align:'center',},
				{field:'userId' ,title:'{{ trans ('systemlanguage.account_apply_userId') }}', width:100, align:'center',},
				{field:'username' ,title:'{{ trans ('systemlanguage.account_apply_userName') }}', width:100, align:'center',},
				{field:'applyamount' ,title:'{{ trans ('systemlanguage.account_apply_amount') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return parseFloatToFixed(value);
				}},
				{field:'actapplyamount' ,title:'{{ trans ('systemlanguage.account_apply_actapplyamount') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return parseFloatToFixed(value);
				}},
				{field:'actdraw' ,title:'{{ trans ('systemlanguage.account_apply_actdraw') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return parseFloatToFixed(value);
				}},
				{field:'drawrate' ,title:'{{ trans ('systemlanguage.account_apply_drawrate') }}', width:100, align:'center',},
				{field:'drawpoundage' ,title:'{{ trans ('systemlanguage.account_apply_drawpoundage') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return parseFloatToFixed(value);
				}},
				{field:'applystatus' ,title:'{{ trans ('systemlanguage.account_apply_status') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return getWithdrawApplyStatus(value);
				}},
				{field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.account_rec_crt_date') }}', width:100, align:'center',},
				{field:'useredit' ,title:'{{ trans ('systemlanguage.account_apply_options') }}' ,width:110, align:'center',formatter: function (value, rowData, rowIndex) {
					if (rowData.applystatus == "0") {
						return '<a href="javascript:;" onclick="withdrawOrderIdDetail('+ rowData.record_id +', '+ rowData.mt4_ticket +')" class="l-btn l-btn-small l-btn-plain" style="color: blue;">' +
									'<span class="l-btn-left l-btn-icon-left">' +
										'<span class="l-btn-text">操作</span>' +
										'<span class="l-btn-icon icon-edit">&nbsp;</span>' +
									'</span>'+
								'</a>';
					} else {
						return '<a href="javascript:;" onclick="withdrawOrderIdDetail('+ rowData.record_id +', '+ rowData.mt4_ticket +')" class="l-btn l-btn-small l-btn-plain">' +
									'<span class="l-btn-left l-btn-icon-left">' +
										'<span class="l-btn-text" style="color: black;">查看</span>' +
										'<span class="l-btn-icon icon-search">&nbsp;</span>' +
									'</span>'+
								'</a>';
					}
				}},
			]];
			
			config.Buttons = [{
				text: '{{ trans ('systemlanguage.export') }}',
				iconCls:'icon-export',
				handler:function(){
					flow_export("AdminWithdrawApplyForm", "withdrawApply", "admin", "{{ csrf_token() }}")
				}
			}];
			
			return config;
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: route_prefix() + "/amount/withdrawApplySearch",
				tableId: "withdraw_data_list",
				formId: "AdminWithdrawApplyForm",
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'mt4_ticket',
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