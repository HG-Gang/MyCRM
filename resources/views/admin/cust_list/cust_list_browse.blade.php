@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<form class="layui-form" action="" id="CustListForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户或身份证号" class="layui-input" style="width: 250px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">账户名称</label>
				<div class="layui-input-block">
					<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入账户名称" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">是否认证</label>
				<div class="layui-input-inline">
					<select name="userstatus" id="userstatus">
						<option value="">请选择状态</option>
						<option value="0">未认证</option>
						<option value="1">已认证</option>
					</select>
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">开户时间</label>
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
	<table id="data_list" style="width: 99%;" pagination="true" title="客户列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field:'mt4_login' ,title:'{{ trans ('systemlanguage.direct_customer_user_id') }}', width:100, align:'center',formatter: function (value, rowData, rowIndex) {
					if (rowData.user_status == '1') {
						return "<span class='l-btn-left l-btn-icon-right' title='已认证'>" +
							"<span class='l-btn-text'>" + value + "</span>" +
							"<span class='l-btn-icon icon-auth-man'>&nbsp;</span>" +
							"</span>";
					}
					
					return "<span class='l-btn-left l-btn-icon-right'><span class='l-btn-text'>" + value + "</span></span>";
				}},
				{field:'user_name' ,title:'{{ trans ('systemlanguage.direct_customer_user_name') }}', width:100, align:'center',},
				{field:'mt4_balance' ,title:'{{ trans ('systemlanguage.direct_customer_user_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					
					return parseFloatToFixed(value);
				}},
				{field:'mt4_equity' ,title:'{{ trans ('systemlanguage.direct_customer_user_eqy_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					
					return parseFloatToFixed(value);
				}},
				{field:'total_yuerj' ,title:'{{ trans ('systemlanguage.direct_customer_user_rj_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					
					return parseFloatToFixed(value);
				}},
				{field:'total_yuecj' ,title:'{{ trans ('systemlanguage.direct_customer_user_qk_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					
					return parseFloatToFixed(value);
				}},
				{field:'total_net_worth' ,title:'{{ trans ('systemlanguage.direct_customer_user_net_moneny') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					
					return parseFloatToFixed(value);
				}},
				{field:'total_comm' ,title:'{{ trans ('systemlanguage.direct_customer_user_poundage_moneny') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					
					return parseFloatToFixed(value);
				}},
				{field:'total_profit' ,title:'{{ trans ('systemlanguage.direct_customer_user_profit_loss') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(value < 0) {
						return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
					}
					
					return parseFloatToFixed(value);
				}},
				{field:'mt4MarginLevel' ,title:'{{ trans ('systemlanguage.proxy_mt4MarginLevel') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if(rowData.user_name) {
						return mt4MarginLevelFormat(value);
					} else {
						return "";
					}
				}},
				{field:'total_noble_metal' ,title:'{{ trans ('systemlanguage.direct_customer_user_noble_metal') }}', width:100, align:'center'},
				{field:'total_for_exca' ,title:'{{ trans ('systemlanguage.direct_customer_user_foreign_exchange') }}', width:100, align:'center'},
				{field:'total_crud_oil' ,title:'{{ trans ('systemlanguage.direct_customer_user_energy') }}', width:100, align:'center'},
				{field:'total_index' ,title:'{{ trans ('systemlanguage.direct_customer_user_index') }}', width:100, align:'center'},
				{field:'total_volume' ,title:'{{ trans ('systemlanguage.direct_customer_user_total_volume') }}', width:100, align:'center'},
				{field:'total_swaps' ,title:'{{ trans ('systemlanguage.direct_customer_user_swap') }}', width:100, align:'center'},
				{field:'mt4_regdate' ,title:'{{ trans ('systemlanguage.direct_customer_user_rec_crt_date') }}', width:120, align:'center'},
				{field:'comm_trans' ,title:'{{ trans ('systemlanguage.account_change_action') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.user_name != "" && ("{{$role}}" == "1" || "{{ $role }}" == "2")) {
						return '<a href="javascript:;" onclick="edit_account_info('+ rowData.user_id +')" class="l-btn l-btn-small l-btn-plain" style="color: blue; cursor: pointer;">' +
									'<span class="l-btn-left l-btn-icon-left">' +
										'<span class="l-btn-text">编辑</span>' +
										'<span class="l-btn-icon icon-edit">&nbsp;</span>' +
									'</span>'+
								'</a>';
					} else if (rowData.user_name == "") {
						return "";
					} else {
						return "======";
					}
				}},
			]];
			
			return config;
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: route_prefix() + '/cust/custListSearch',
				tableId: 'data_list',
				formId: 'CustListForm',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'user_id',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
				footerMsg: "双击行显示详情",
			});
			
			pagerData.GridInit();
		}
		
		//双击查看直属代理商信息
		function DbClickEditAccountInfo(rowIndex, rowData) {
			show_direct_cust_info(rowData.user_id, "admin");
		}
		
		function autoSearchExtraParam() {
			subPuid.searchtype = 'atuoSearch';
		}
		</script>
@endsection