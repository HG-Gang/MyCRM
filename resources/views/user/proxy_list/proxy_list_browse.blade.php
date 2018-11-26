@extends('user.layout.main_right')

@section('public-resources')
@endsection

@section('content')
	<form class="layui-form" action="" id="proxyListForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">账户名称</label>
				<div class="layui-input-block">
				<input type="text" name="username" id="username" autocomplete="off" placeholder="请输入订单号" class="layui-input" style="width: 200px;">
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
			<button type="button" class="layui-btn" onclick="searchResult()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<div id="real" style="margin-left: 20px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="代理列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		$(function () {
			autoSearchExtraParam();
			createTable();
		});
		
		function searchResult() {
			subPuid = {};//清空对象之前的值
			$("#real").html(""); //重置我的位置
			clickSearchExtraParam();
			createTable();
		}
		
		//查看直属下级代理商
		function DirectSubAgentsDetail(uid) {
			subPuid = {};//清空对象之前的值
			SubSearchExtraParam(uid);
			getUserRelationShip(uid, 'agents', 'DirectSubAgentsDetail', "{{ csrf_token() }}")
			createTable();
		}
		
		//查看直属下级客户
		function DirectSubCustDetail(puid) {
			show_proxy_direct_cust_detail(puid);
		}
		
		//双击查看直属代理商信息
		function DbClickEditAccountInfo(rowIndex, rowData) {
			if (rowData.parent_id == "{{ $_user_info['user_id'] }}") {
				show_direct_cust_info(rowData.user_id, "agents");
			}
			return;
		}
		
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
				{field: 'user_id', title: '{{ trans ('systemlanguage.proxy_user_id') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.user_status == '1') {
						return "<span class='l-btn-left l-btn-icon-right' title='已认证'>" +
									"<span class='l-btn-text'>" + value + "</span>" +
									"<span class='l-btn-icon icon-auth-man'>&nbsp;</span>" +
								"</span>";
					}
					return "<span class='l-btn-left l-btn-icon-right'><span class='l-btn-text'>" + value + "</span></span>";
				}},
				{field: 'user_name', title: '{{ trans ('systemlanguage.proxy_user_name') }}', width: 100, align: 'center',},
				{field: 'agentsTotal', title: '{{ trans ('systemlanguage.proxy_direct_count') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (value > 0) {
						return "<span style='cursor:pointer; color:blue;' onclick='DirectSubAgentsDetail(" + rowData.user_id + ")' title='直属代理商总数'>" + value + "</span>"
					}
					return value;
				}},
				{field: 'accountTotal', title: '{{ trans ('systemlanguage.proxy_cust_count') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (value > 0) {
						return "<span onclick='DirectSubCustDetail(" + rowData.user_id + ")' style='cursor:pointer; color:blue;' title='直属客户总数'>" + value + "</span>"
					}
					return value;
				}},
				{field: 'user_money', title: '{{ trans ('systemlanguage.proxy_user_money') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (value < 0) {
						return "<span style='color: red;'>" + parseFloatToFixed(value) + "</span>";
					}
					return parseFloatToFixed(value);
				}},
				{field: 'cust_eqy', title: '{{ trans ('systemlanguage.proxy_cust_eqy') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (value < 0) {
						return "<span style='color: red;'>" + parseFloatToFixed(value) + "</span>";
					}
					return parseFloatToFixed(value);
				}},
				{field: 'fy_money', title: '{{ trans ('systemlanguage.proxy_fy_money') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (value < 0) {
						return "<span style='color: red;'>" + parseFloatToFixed(value) + "</span>";
					}
					return parseFloatToFixed(value);
				}},
				{field: 'rj_money', title: '{{ trans ('systemlanguage.proxy_rj_money') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (value < 0) {
						return "<span style='color: red;'>" + parseFloatToFixed(value) + "</span>";
					}
					return parseFloatToFixed(value);
				}},
				{field: 'qk_money', title: '{{ trans ('systemlanguage.proxy_qk_money') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (value < 0) {
						return "<span style='color: red;'>" + parseFloatToFixed(value) + "</span>";
					}
					return parseFloatToFixed(value);
				}},
				{field:'rights' ,title:'{{ trans ('systemlanguage.proxy_agents_commp_rights') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
						if (rowData.user_name) {
							return rowData.commprop + ' / ' + value;
						} else {
							return '';
						}
					}},
				{field: 'rec_crt_date', title: '{{ trans ('systemlanguage.proxy_rec_crt_date') }}', width: 100, align: 'center',},
				/*{field: 'comm_trans', title: '{{ trans ('systemlanguage.proxy_comm_trans') }}', width: 100, align: 'center', formatter: function (value, rowData, rowIndex) {
					if (rowData.parent_id == '{{ $_user_info['user_id'] }}') {
						//是直属客户
						return "<font color='blue' onclick='agentsCommissionTransfer(" + rowData.user_id + ")'>" + '转给TA' + "</font>";
					} else if (rowData.user_name == '') {
						return value;
					}
					
					return "======";
				}},*/
			]];
			
			return config;
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: '/user/proxy/proxyListSearch',
				tableId: 'data_list',
				formId: 'proxyListForm',
				method: 'post',
				columns: config.DataColumns,
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
		
		function agentsCommissionTransfer(uid) {
			directProxyCustomerCommissionTransfer(uid);
		}
		
		function autoSearchExtraParam() {
			subPuid.searchtype = 'autoSearch';
		}
		
		function clickSearchExtraParam() {
			subPuid.searchtype = 'clickSearch';
		}
		
		function SubSearchExtraParam(uid) {
			subPuid.searchtype = 'subSearch';
			subPuid.userPId = uid;
		}
	</script>
@endsection