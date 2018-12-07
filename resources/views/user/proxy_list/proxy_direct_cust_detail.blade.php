@extends('user.layout.main_right')

@section('public-resources')

@endsection

@section('content')
	<div style="margin:20px 0px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="直属客户列表"></table>
@endsection

@section('custom-resources')
	<script type="text/javascript">
		$(function () {
			autoSearchExtraParam();
			createTable();
		});
		
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [[
			{field:'user_group' ,title:'{{ trans ('systemlanguage.proxy_direct_user_group') }}', width:100, align:'center',},
			{field:'user_id' ,title:'{{ trans ('systemlanguage.proxy_direct_user_id') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				if (rowData.user_status == '1') {
					return "<span class='l-btn-left l-btn-icon-right' title='已认证'>" +
						"<span class='l-btn-text'>"+ value +"</span>" +
						"<span class='l-btn-icon icon-auth-man'>&nbsp;</span>" +
						"</span>";
					}
				
				return "<span class='l-btn-left l-btn-icon-right'><span class='l-btn-text'>"+ value +"</span></span>";
			}},
			{field:'user_name' ,title:'{{ trans ('systemlanguage.proxy_direct_user_name') }}', width:100, align:'center',},
			{field:'user_money' ,title:'{{ trans ('systemlanguage.proxy_direct_user_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				if(value < 0) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}
				
				return parseFloatToFixed(value);
			}},
			{field:'cust_eqy' ,title:'{{ trans ('systemlanguage.proxy_direct_user_eqy') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				if(value < 0) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}
				
				return parseFloatToFixed(value);
			}},
			{field:'total_yuerj' ,title:'{{ trans ('systemlanguage.proxy_direct_user_rj_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				if(value < 0) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}
				
				return parseFloatToFixed(value);
			}},
			{field:'total_yuecj' ,title:'{{ trans ('systemlanguage.proxy_direct_user_qk_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				if(value < 0) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}
				
				return parseFloatToFixed(value);
			}},
			{field:'total_net_worth' ,title:'{{ trans ('systemlanguage.proxy_direct_user_net_money') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				if(value < 0) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}
				
				return parseFloatToFixed(value);
			}},
			{field:'total_comm' ,title:'{{ trans ('systemlanguage.proxy_direct_user_poundage_moneny') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				if(value < 0) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}
				
				return parseFloatToFixed(value);
			}},
			{field:'total_profit' ,title:'{{ trans ('systemlanguage.proxy_direct_user_profit_loss') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				if(value < 0) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}
				
				return parseFloatToFixed(value);
			}},
			{field:'mt4MarginLevel' ,title:'{{ trans ('systemlanguage.proxy_mt4MarginLevel') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
				if (rowData.user_name) {
					return mt4MarginLevelFormat(value);
				} else {
					return "";
				}
			}},
			{field:'total_noble_metal' ,title:'{{ trans ('systemlanguage.proxy_direct_user_noble_metal') }}', width:100, align:'center',},
			{field:'total_for_exca' ,title:'{{ trans ('systemlanguage.proxy_direct_user_foreign_exchange') }}', width:100, align:'center',},
			{field:'total_crud_oil' ,title:'{{ trans ('systemlanguage.proxy_direct_user_energy') }}', width:100, align:'center',},
			{field:'total_index' ,title:'{{ trans ('systemlanguage.proxy_direct_user_index') }}', width:100, align:'center',},
			{field:'total_volume' ,title:'{{ trans ('systemlanguage.proxy_direct_user_total_volume') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				if(value < 0) {
					return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
				}
				
				return parseFloatToFixed(value);
			}},
			{field:'total_swaps' ,title:'{{ trans ('systemlanguage.proxy_direct_user_swap') }}', width:100, align:'center', formatter: function (value, rowData, rowIndex) {
				return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
			}},
			{field:'rec_crt_date' ,title:'{{ trans ('systemlanguage.proxy_direct_user_crtdate') }}',width:100, align:'center',},
			]];
			
			return config;
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
				pagerData = new $.WidgetPage({
				reqUrl: '/user/proxy/direct_cust_detail_list',
				tableId: 'data_list',
				method: 'post',
				columns : config.DataColumns,
				formToken: "{{ csrf_token() }}",
				idField: 'user_id',
				extraParam: subPuid,
				rownumbers: true,
				singleSelect: true,
				showFooter: true,
			});
			
			pagerData.GridInit();
		}
		
		function autoSearchExtraParam() {
			subPuid.searchtype = 'atuoSearch';
			subPuid.puid = '{{ $puid }}';
		}
		
		//双击更改直属客户组别信息
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log('没有可查看的更多信息了');
		}
	</script>
@endsection