@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<input type="hidden" id="role" value="{{ $_user_info['user_id'] }}">
	<div class="layui-tab layui-tab-brief" lay-filter="customer_flow">
		<ul class="layui-tab-title">
			<li class="layui-this" data-tab_id="deposit_flow">入金流水</li>
			<li data-tab_id="withdrawal_flow">出金流水</li>
			<li data-tab_id="withdrawal_apply_flow">出金申请</li>
			@if($_role_type == 'Agents')
				<li data-tab_id="direct_deposit_flow">直属入金流水</li>
				<li data-tab_id="direct_withdrawal_flow">直属出金流水</li>
			@endif
		</ul>
		<div class="layui-tab-content" style="width: 100%; height: 100%;">
			<div class="layui-tab-item layui-show">@include("user.customer_flow.deposit_flow_browse")</div>
			<div class="layui-tab-item">@include("user.customer_flow.withdraw_flow_browse")</div>
			<div class="layui-tab-item">@include("user.customer_flow.withdraw_apply_browse")</div>
			@if($_role_type == 'Agents')
				<div class="layui-tab-item">@include("user.customer_flow.direct_deposit_browse")</div>
				<div class="layui-tab-item">@include("user.customer_flow.direct_withdraw_browse")</div>
			@endif
		</div>
	</div>
@endsection

@section('custom-resources')
	<script>
		//入金
		function deposit_flow() {
			tabPanlSearch.reqUrl = '/user/flow/depositFlowSearch';
			tabPanlSearch.tableId = 'deposit_data_list';
			tabPanlSearch.formId = 'DepositFlowForm';
			tabPanlSearch.DataColumns = [[
				{field:'order_no' ,title:'{{ trans ('systemlanguage.account_deposit_order_no') }}', width:100, align:'center',},
				{field:'userId' ,title:'{{ trans ('systemlanguage.account_deposit_no') }}', width:100, align:'center',},
				{field:'depositType' ,title:'{{ trans ('systemlanguage.account_deposit_type') }}', width:100, align:'center',},
				{field:'depositComment' ,title:'{{ trans ('systemlanguage.account_deposit_comment') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return getAccountdepositType(rowData.depositType);
				}},
				{field:'depositActProfit' ,title:'{{ trans ('systemlanguage.account_deposit_moneny') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					return parseFloatToFixed(value);
				}},
				{field:'modify_time' ,title:'{{ trans ('systemlanguage.account_deposit_datetme') }}', width:100, align:'center',},
			]];
			
			createTable();
		}
		
		//出金
		function withdrawal_flow() {
			tabPanlSearch.reqUrl = '/user/flow/withdrawalFlowSearch';
			tabPanlSearch.tableId = 'withdrawal_data_list';
			tabPanlSearch.formId = 'WithdrawFlowForm';
			tabPanlSearch.DataColumns = [[
				{field:'order_no' ,title:'{{ trans ('systemlanguage.account_withdrawal_order_no') }}', width:100, align:'center',},
				{field:'userId' ,title:'{{ trans ('systemlanguage.account_withdrawal_no') }}', width:100, align:'center',},
				{field:'withdrawalType' ,title:'{{ trans ('systemlanguage.account_withdrawal_type') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.userId != "") {
						return getAccountdepositType(value);
					}
				
					return value;
				}},
				{field:'withdrawalActProfit' ,title:'{{ trans ('systemlanguage.account_withdrawal_moneny') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
						if(value < 0) {
							return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
						}
						return parseFloatToFixed(value);
					}},
				{field:'withdrawalDate' ,title:'{{ trans ('systemlanguage.account_withdrawal_datetme') }}', width:100, align:'center',},
			]];
			
			createTable();
		}
		
		//出金申请
		function withdrawal_apply_flow() {
			tabPanlSearch.reqUrl = '/user/flow/withdrawApplyFlowSearch';
			tabPanlSearch.tableId = 'withdraw_apply_data_list';
			tabPanlSearch.formId = 'WithdrawApplyFlowForm';
			tabPanlSearch.DataColumns = [[
				{field:'order_no' ,title:'{{ trans ('systemlanguage.account_apply_order_no') }}', width:100, align:'center',},
				{field:'userId' ,title:'{{ trans ('systemlanguage.account_apply_userId') }}', width:100, align:'center',},
				{field:'userName' ,title:'{{ trans ('systemlanguage.account_withdrawal_no') }}', width:100, align:'center',},
				{field:'applyamount' ,title:'{{ trans ('systemlanguage.account_apply_amount') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					return parseFloatToFixed(value);
				}},
				/*{field:'actapplyamount' ,title:'{{ trans ('systemlanguage.account_apply_actapplyamount') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					return parseFloatToFixed(value);
				}},*/
				{field:'drawrate' ,title:'{{ trans ('systemlanguage.account_apply_drawrate') }}', width:100, align:'center',},
				{field:'drawbankno' ,title:'{{ trans ('systemlanguage.account_apply_drawbankno') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return bankNoFormat(value);
				}},
				{field:'drawbankclass' ,title:'{{ trans ('systemlanguage.account_apply_drawbankclass') }}', width:100, align:'center',},
				{field:'applystatus' ,title:'{{ trans ('systemlanguage.account_apply_status') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.userId != "") {
						return getDrawApplyStatus(value);
					}
					return value;
				}},
				{field:'applyremark' ,title:'{{ trans ('systemlanguage.account_withdrawal_fail_reason') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.applystatus == "3") {
						return '<a href="javascript:;" onclick="showFailReason('+ rowData.order_no +', '+ "'"+ value +"'"+')" class="l-btn l-btn-small l-btn-plain">' +
							'<span class="l-btn-left l-btn-icon-left">' +
							'<span class="l-btn-text" style="color: black;">查看</span>' +
							'<span class="l-btn-icon icon-search">&nbsp;</span>' +
							'</span>'+
							'</a>';
					} else if (rowData.applystatus != "3" && rowData.userId != "") {
						return "========";
					} else {
						return "";
					}
                }},
				{field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.account_withdrawal_datetme') }}', width:100, align:'center',},
			]];
			
			createTable();
		}
		
		//直属入金
		function direct_deposit_flow() {
			tabPanlSearch.reqUrl = '/user/flow/directDepositFlowSearch';
			tabPanlSearch.tableId = 'direct_deposit_data_list';
			tabPanlSearch.formId = 'DirectDepositFlowForm';
			tabPanlSearch.DataColumns = [[
				{field:'order_no' ,title:'{{ trans ('systemlanguage.account_deposit_order_no') }}', width:100, align:'center', },
				{field:'userId' ,title:'{{ trans ('systemlanguage.account_deposit_no') }}', width:100, align:'center', },
				{field:'directType' ,title:'{{ trans ('systemlanguage.account_deposit_type') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					return getAccountdepositType(rowData.directType);
				}},
				{field:'directProfit' ,title:'{{ trans ('systemlanguage.account_deposit_moneny') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					return parseFloatToFixed(value);
				}},
				{field:'directComment' ,title:'{{ trans ('systemlanguage.account_deposit_source') }}', width:100, align:'center', },
				{field:'directModifyTime' ,title:'{{ trans ('systemlanguage.account_deposit_datetme') }}', width:100, align:'center', },
			]];
		
		tabPanlSearch.Buttons = [{
			text: '{{ trans ('systemlanguage.export') }}',
			iconCls:'icon-export',
			handler:function(){
				flow_export("DirectDepositFlowForm", "depositFlow", "agents", "{{ csrf_token() }}")
			}
		}];
		
			createTable();
		}
		
		//直属出金
		function direct_withdrawal_flow() {
			tabPanlSearch.reqUrl = '/user/flow/directWithdrawalFlowSearch';
			tabPanlSearch.tableId = 'direct_withdrawal_data_list';
			tabPanlSearch.formId = 'DirectWithdrawFlowForm';
			tabPanlSearch.DataColumns = [[
				{field:'order_no' ,title:'{{ trans ('systemlanguage.account_withdrawal_order_no') }}', width:100, align:'center',},
				{field:'userId' ,title:'{{ trans ('systemlanguage.account_withdrawal_no') }}', width:100, align:'center',},
				{field:'directdrawalComment' ,title:'{{ trans ('systemlanguage.account_withdrawal_type') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.userId != "") {
						return getAccountdepositType(value);
					}
					return value;
				}},
				{field:'directdrawalActProfit' ,title:'{{ trans ('systemlanguage.account_withdrawal_moneny') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					return parseFloatToFixed(value);
				}},
				{field:'directdrawalModifyTime' ,title:'{{ trans ('systemlanguage.account_withdrawal_datetme') }}', width:100, align:'center',},
			]];
			
			createTable();
		}
		
		function createTable() {
			var pagerData;
			pagerData = new $.WidgetPage({
				//title: ajaxGetTableTitle(),
				reqUrl: tabPanlSearch.reqUrl,
				tableId: tabPanlSearch.tableId,
				formId: tabPanlSearch.formId,
				method: 'post',
				columns : tabPanlSearch.DataColumns,
				buttons: tabPanlSearch.Buttons,
				formToken: "{{ csrf_token() }}",
				idField: 'ticket_id',
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
		
		function showFailReason(orderId, value) {
			layer.open({
				type: 1,
				area: ['400px', '200px'],
				title: "订单 " + orderId + " 的出金失败详情",
				anim: 5,
				move: false,
				skin: 'layui-layer-molv', //样式类名
				content: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + value,
			});
		}
	</script>
@endsection