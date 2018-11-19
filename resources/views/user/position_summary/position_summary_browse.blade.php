@extends('user.layout.main_right')

@section('content')
	<form class="layui-form" action="" id="positionSummaryForm" style="margin-top: 8px;">
		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">交易账户</label>
				<div class="layui-input-block">
					<input type="text" name="userId" id="userId" autocomplete="off" placeholder="请输入交易账户" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">账户姓名</label>
				<div class="layui-input-block">
					<input type="text" name="userName" id="userName" autocomplete="off" placeholder="请输入账户姓名" class="layui-input" style="width: 200px;">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">汇总时间</label>
					<div class="layui-input-inline" style="width: 200px;">
						<input type="text" name="startdate" id="startdate" placeholder="请输入开始时间" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid">-</div>
					<div class="layui-input-inline" style="width: 200px;">
						<input type="text" name="enddate" id="enddate" placeholder="请输入结束时间" autocomplete="off" class="layui-input">
					</div>
			</div>
			<button type="button" class="layui-btn" onclick="searchPositionSummary()">查找</button>
		</div>
	</form>
	<div style="margin:20px 0px;"></div>
	<div id="real" style="margin-left: 20px;"></div>
	<table id="data_list" style="width: 99%;" pagination="true" title="仓位总结"></table>
@section('custom-resources')
	<script type="text/javascript">
		$(function () {
			autoSearchExtraParam();
			createTable();
		});
		
		function searchPositionSummary() {
			subPuid = {};//清空对象之前的值
			$("#real").html(""); //重置我的位置
			clickSearchExtraParam();
			createTable();
		}
		
		function dataGridConfig() {
			var config = {};
			config.DataColumns = [
				[
					{field:'user_id' ,title:'{{ trans ('systemlanguage.position_summary_user_id') }}', width:100, align:'center', rowspan:2, formatter: function (value, rowData, rowIndex) {
							return "<font  onclick='subAgentsPositionSummary("+ value +")'; color='blue' title='查看详情'>" + value + "</font>";
					}},
					{field:'user_name' ,title:'{{ trans ('systemlanguage.position_summary_user_name') }}', width:100, align:'center', rowspan:2,},
					{field:'total_yuerj' ,title:'{{ trans ('systemlanguage.position_summary_deposit_moneny') }}', width:100, align:'center', rowspan:2,},
					{field:'total_yuecj' ,title:'{{ trans ('systemlanguage.position_summary_withdrawal_moneny') }}', width:100, align:'center', rowspan:2, formatter: function (value, rowData, rowIndex) {
						if(value < 0) {
							return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
						}
						return parseFloatToFixed(value);
					}},
					{field:'total_rebate' ,title:'{{ trans ('systemlanguage.position_summary_comm_moneny') }}', width:100, align:'center', rowspan:2,},
					{field:'total_net_worth',title:'{{ trans ('systemlanguage.position_summary_net_deposit_moneny') }}', width:100, align:'center', rowspan:2,},
					{field:'total_comm' ,title:'{{ trans ('systemlanguage.position_summary_poundage_moneny') }}', width:100, align:'center', rowspan:2,},
					{field:'total_profit' ,title:'{{ trans ('systemlanguage.position_summary_profit_loss') }}' ,width:100, align:'center', rowspan:2, formatter: function (value, rowData, rowIndex) {
						if(value < 0) {
							return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
						}
						return parseFloatToFixed(value);
					}},
					{title:'{{ trans ('systemlanguage.position_summary_product_type') }}', width:400, colspan: 4},
					{field:'total_volume' ,title:'{{ trans ('systemlanguage.position_summary_total_volume') }}', width:100, align:'center', rowspan:2, formatter: function (value, rowData, rowIndex) {
						return parseFloatToFixed(value);
					}},
					{field:'total_swaps' ,title:'{{ trans ('systemlanguage.position_summary_swap') }}', width:100, align:'center', rowspan:2, formatter: function (value, rowData, rowIndex) {
						if(value < 0) {
							return "<span style='color: red;'>"+ parseFloatToFixed(value) +"</span>";
						}
						return parseFloatToFixed(value);
					}},
				],
				[
					{field:'total_noble_metal' ,title:'{{ trans ('systemlanguage.position_summary_noble_metal') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
						return parseFloatToFixed(value);
					}},
					{field:'total_for_exca' ,title:'{{ trans ('systemlanguage.position_summary_energy') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
						return parseFloatToFixed(value);
					}},
					{field:'total_crud_oil' ,title:'{{ trans ('systemlanguage.position_summary_foreign_exchange') }}' ,width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
						return parseFloatToFixed(value);
					}},
					{field:'total_index' ,title:'{{ trans ('systemlanguage.position_summary_index') }}', width:100 ,align:'center', formatter: function (value, rowData, rowIndex) {
						return parseFloatToFixed(value);
					}},
				],
			];
			
			return config;
		}
		
		function subAgentsPositionSummary(uid) {
			subPuid = {};
			getUserRelationShip(uid, "agents", "subAgentsPositionSummary", "{{ csrf_token() }}");
			getSubExtraParam(uid);
			createTable();
		}
		
		function DbClickEditAccountInfo(rowIndex, rowData) {
			console.log("没有可查看的信息!");
		}
		
		function aEditAccountInfo(index, uid) {
			alert('index:' + index + 'Uid: ' + uid);
		}
		
		//合并单元格
		function mergesCellByField(rowData) {
			var merges = [{
				field: 'user_money',
				index:3,
				colspan:2,
			}, {
				field: 'user_id',
				index: 18,
				rowspan: 3,
			}];
			
			for(var i=0; i<merges.length; i++) {
				//data_list
				$("#data_list").datagrid('mergeCells',{
					index: merges[i].index,
					field: merges[i].field,
					rowspan: merges[i].rowspan,
					colspan: merges[i].colspan,
				});
			}
		}
		
		function createTable() {
			var config = dataGridConfig(), pagerData;
			pagerData = new $.WidgetPage({
				reqUrl: '/user/position/positionSummarySearch',
				tableId: 'data_list',
				formId: 'positionSummaryForm',
				method: 'post',
				columns : config.DataColumns,
				buttons: config.Buttons,
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
			subPuid.searchtype = 'autoSearch';
		}
		
		function clickSearchExtraParam() {
			subPuid.searchtype = 'clickSearch';
		}
		
		function getSubExtraParam(uid) {
			subPuid.searchtype = "subAgentsSearch";
			subPuid.userPId = uid;
		}
	</script>
@endsection